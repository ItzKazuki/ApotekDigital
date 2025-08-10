<?php

namespace App\Http\Controllers\Kasir;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;

class ReportController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('member', 'kasir', 'transactionDetails')
            ->orderBy('transaction_date', 'desc')
            ->whereDate('transaction_date', today())
            ->paginate(5);

        $cashToday = Transaction::whereDate('transaction_date', today())
            ->where('payment_method', 'tunai')
            ->sum('cash');

        $cashYesterday = Transaction::whereDate('transaction_date', today()->subDay())
            ->where('payment_method', 'tunai')
            ->sum('cash');

        if ($cashYesterday > 0) {
            $percentageCashChange = (($cashToday - $cashYesterday) / $cashYesterday) * 100;
        } else {
            $percentageCashChange = 0;
        }

        $changeToday = Transaction::whereDate('transaction_date', today())
            ->sum('change');

        $totalQuantityToday = Transaction::whereDate('transaction_date', today())
            ->with('transactionDetails')
            ->get()
            ->sum(function ($transaction) {
                return $transaction->transactionDetails->sum('quantity');
            });

        $totalProfitToday = Transaction::whereDate('transaction_date', today())
            ->with('transactionDetails.drug') // load drug juga
            ->get()
            ->sum(function ($transaction) {
                return $transaction->transactionDetails->sum(function ($detail) {
                    $sellingPrice = $detail->drug->price; // harga jual
                    $buyingPrice  = $detail->drug->purchase_price; // harga modal
                    return $detail->quantity * ($sellingPrice - $buyingPrice);
                });
            });

        // === Profit Yesterday ===
        $totalProfitYesterday = Transaction::whereDate('transaction_date', today()->subDay())
            ->with('transactionDetails.drug')
            ->get()
            ->sum(function ($transaction) {
                return $transaction->transactionDetails->sum(function ($detail) {
                    $sellingPrice = $detail->drug->price;
                    $buyingPrice  = $detail->drug->purchase_price;
                    return $detail->quantity * ($sellingPrice - $buyingPrice);
                });
            });

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $data = Transaction::select(
            DB::raw('DAYNAME(transaction_date) as day_name'),
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(total) as total_revenue')
        )
            ->whereBetween('transaction_date', [$startOfWeek, $endOfWeek])
            ->groupBy('day_name')
            ->orderByRaw("FIELD(day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->get();

        // Biar urutan harinya sesuai dan nilai kosong diisi 0
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $transactionsPerDay = [];
        $revenuePerDay = [];

        $dayMap = [
            'Senin'    => 'Monday',
            'Selasa'   => 'Tuesday',
            'Rabu'     => 'Wednesday',
            'Kamis'    => 'Thursday',
            'Jumat'    => 'Friday',
            'Sabtu'    => 'Saturday',
            'Minggu'   => 'Sunday',
        ];

        foreach ($days as $day) {
            $found = $data->firstWhere('day_name', $dayMap[$day]);
            $transactionsPerDay[] = $found ? $found->total_transactions : 0;
            $revenuePerDay[] = $found ? round($found->total_revenue / 1_000_000, 2) : 0;
        }

        // === Percentage Change ===
        if ($totalProfitYesterday > 0) {
            $percentageProfitChange = (($totalProfitToday - $totalProfitYesterday) / $totalProfitYesterday) * 100;
        } else {
            $percentageProfitChange = 0;
        }

        $percentageProfitChange = round($percentageProfitChange, 1);

        $percentageCashChange = round($percentageCashChange, 1); // 1 desimal

        return view('kasir.report', compact('transactions', 'cashToday', 'changeToday', 'totalQuantityToday', 'totalProfitToday', 'transactionsPerDay', 'revenuePerDay', 'percentageCashChange', 'percentageProfitChange'));
    }

    public function exportTransactionsWithChart()
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();

        // Ambil data transaksi + detail + drugs
        $data = DB::table('transactions as t')
            ->join('transaction_details as td', 't.id', '=', 'td.transaction_id')
            ->join('drugs as d', 'td.drug_id', '=', 'd.id')
            ->select(
                't.id as transaction_id',
                't.transaction_date',
                'd.name as drug_name',
                'td.quantity',
                'td.price',
                DB::raw('(td.price * td.quantity) as total_sale'),
                DB::raw('(d.modal * td.quantity) as total_cost'),
                DB::raw('(td.price * td.quantity) - (d.modal * td.quantity) as net_profit')
            )
            ->orderBy('t.transaction_date')
            ->get();

        // Kelompokkan per transaksi tanggal
        $summary = [];
        foreach ($data as $row) {
            $date = $row->transaction_date;
            if (!isset($summary[$date])) {
                $summary[$date] = [
                    'gross_profit' => 0,
                    'net_profit' => 0
                ];
            }
            $summary[$date]['gross_profit'] += $row->total_sale;
            $summary[$date]['net_profit'] += $row->net_profit;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Isi data detail transaksi
        $sheet->setCellValue('A1', 'Transaction ID');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Drug Name');
        $sheet->setCellValue('D1', 'Quantity');
        $sheet->setCellValue('E1', 'Price');
        $sheet->setCellValue('F1', 'Total Sale');
        $sheet->setCellValue('G1', 'Total Cost');
        $sheet->setCellValue('H1', 'Net Profit');

        $rowNum = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $rowNum, $item->transaction_id);

            $sheet->setCellValue('B' . $rowNum, $item->transaction_date);
            $sheet->setCellValue('C' . $rowNum, $item->drug_name);
            $sheet->setCellValue('D' . $rowNum, $item->quantity);
            $sheet->setCellValue('E' . $rowNum, $item->price);
            $sheet->setCellValue('F' . $rowNum, $item->total_sale);
            $sheet->setCellValue('G' . $rowNum, $item->total_cost);
            $sheet->setCellValue('H' . $rowNum, $item->net_profit);
            $rowNum++;
        }

        // Sheet kedua untuk summary chart
        $chartSheet = $spreadsheet->createSheet();
        $chartSheet->setTitle('Summary');

        $chartSheet->setCellValue('A1', 'Date');
        $chartSheet->setCellValue('B1', 'Gross Profit');
        $chartSheet->setCellValue('C1', 'Net Profit');

        $r = 2;
        foreach ($summary as $date => $sum) {
            $chartSheet->setCellValue('A' . $r, $date);
            $chartSheet->setCellValue('B' . $r, $sum['gross_profit']);
            $chartSheet->setCellValue('C' . $r, $sum['net_profit']);
            $r++;
        }

        // Chart
        $labelGross = [new DataSeriesValues('String', 'Summary!$B$1', null, 1)];
        $labelNet = [new DataSeriesValues('String', 'Summary!$C$1', null, 1)];

        $categories = [new DataSeriesValues('String', 'Summary!$A$2:$A$' . ($r - 1), null, ($r - 2))];

        $valuesGross = [new DataSeriesValues('Number', 'Summary!$B$2:$B$' . ($r - 1), null, ($r - 2))];
        $valuesNet = [new DataSeriesValues('Number', 'Summary!$C$2:$C$' . ($r - 1), null, ($r - 2))];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($valuesGross) + count($valuesNet) - 1),
            array_merge($labelGross, $labelNet),
            $categories,
            array_merge($valuesGross, $valuesNet)
        );

        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Gross vs Net Profit');

        $chart = new Chart('chart1', $title, $legend, $plotArea);
        $chart->setTopLeftPosition('E2');
        $chart->setBottomRightPosition('M20');

        $chartSheet->addChart($chart);

        // Output
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        $filename = "transaction_report_{$startDate}.xlsx";
        $path = storage_path($filename);
        $writer->save($path);

        return response()->download($path)->deleteFileAfterSend();
    }
}
