<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();

        // Data transaksi sesuai filter tanggal
        $transactions = Transaction::with('transactionDetails.drug')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('status', Transaction::STATUS_PAID)
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Ringkasan
        $totalSales = $transactions->sum('total');
        $totalTransactions = $transactions->count();
        $totalProductsSold = $transactions->flatMap->transactionDetails->sum('quantity');
        $totalProfit = $transactions->flatMap->transactionDetails->sum(function ($detail) {
            return ($detail->price - $detail->drug->modal) * $detail->quantity;
        });

        return view('admin.report.index', compact(
            'transactions',
            'startDate',
            'endDate',
            'totalSales',
            'totalTransactions',
            'totalProductsSold',
            'totalProfit',
            'startDate',
            'endDate'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $chartBase64 = null;
        $start = $request->start_at ?? now()->startOfMonth()->toDateString();
        $end = $request->end_at ?? now()->endOfMonth()->toDateString();

        $transactions = Transaction::with('transactionDetails.drug')
            ->whereBetween('transaction_date', [$start, $end])
            ->where('status', Transaction::STATUS_PAID)
            ->get();

        $totalOmzet = $transactions->sum('total');

        $totalKeuntungan = $transactions->flatMap->transactionDetails->sum(function ($detail) {
            return ($detail->price - $detail->drug->modal) * $detail->quantity;
        });

        $jumlahTransaksi = $transactions->count();

        $produkTerjual = $transactions->flatMap->transactionDetails->sum('quantity');

        // cek jika ada internet maka eksekusi untuk show chart nya.

        // Hitung total penjualan & keuntungan per tanggal
        $grouped = $transactions->groupBy('transaction_date')->map(function ($transaksiPerTanggal) {
            $totalPenjualan = 0;
            $totalKeuntungan = 0;

            foreach ($transaksiPerTanggal as $trx) {
                $totalPenjualan += $trx->total;

                foreach ($trx->transactionDetails as $detail) {
                    $hargaJual = $detail->price;
                    $modal = $detail->drug->modal ?? 0;
                    $keuntunganPerProduk = ($hargaJual - $modal) * $detail->quantity;
                    $totalKeuntungan += $keuntunganPerProduk;
                }
            }

            return [
                'totalPenjualan' => $totalPenjualan,
                'totalKeuntungan' => $totalKeuntungan,
            ];
        });

        // Susun data chart
        $chartData = [
            'labels' => $grouped->keys()->map(function ($date) {
                return Carbon::parse($date)->format('d M Y');
            })->toArray(),
            'totalPenjualan' => $grouped->pluck('totalPenjualan')->toArray(),
            'keuntungan' => $grouped->pluck('totalKeuntungan')->toArray(),
        ];

        // Gunakan QuickChart API
        $chartConfig = [
            'type' => 'line',
            'data' => [
                'labels' => $chartData['labels'],
                'datasets' => [
                    [
                        'label' => 'Total Penjualan',
                        'borderColor' => '#007bff',
                        'fill' => false,
                        'data' => $chartData['totalPenjualan']
                    ],
                    [
                        'label' => 'Keuntungan',
                        'borderColor' => '#28a745',
                        'fill' => false,
                        'data' => $chartData['keuntungan']
                    ]
                ]
            ]
        ];

        $chartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartConfig));
        $chartImage = file_get_contents($chartUrl);
        $chartBase64 = 'data:image/png;base64,' . base64_encode($chartImage);


        $pdf = Pdf::loadView('pdf.report', [
            'transactions' => $transactions,
            'totalOmzet' => $totalOmzet,
            'totalKeuntungan' => $totalKeuntungan,
            'jumlahTransaksi' => $jumlahTransaksi,
            'produkTerjual' => $produkTerjual,
            'start' => $start,
            'end' => $end,
            'chartBase64' => $chartBase64
        ]);

        return $pdf->download("laporan_{$start}_{$end}.pdf");
    }
}
