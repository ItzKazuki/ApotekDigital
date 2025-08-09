<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSalesToday = Transaction::whereDate('transaction_date', today())
            ->sum('total');

        $totalTransactions = Transaction::count();

        $membersRegistered = Member::count();

        $today = Carbon::today();

        $profitToday = TransactionDetail::whereHas('transaction', function ($query) use ($today) {
            $query->whereDate('transaction_date', $today);
        })->join('drugs', 'transaction_details.drug_id', '=', 'drugs.id')
            ->select(DB::raw('SUM((transaction_details.price - drugs.modal) * transaction_details.quantity) AS total_profit'))
            ->value('total_profit');

        $topSellingProducts = TransactionDetail::with('drug.category') // eager load drug dan category-nya
            ->select('drug_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('drug_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'name' => $item->drug->name,
                    'image_path' => $item->drug->image_path,
                    'category' => $item->drug->category->name ?? '-',
                    'total_sold' => $item->total_quantity,
                ];
            });

        $dailyData = $this->getChartData('daily');
        $weeklyData = $this->getChartData('weekly');
        $monthlyData = $this->getChartData('monthly');

        return view('admin.dashboard', [
            'totalSalesToday' => $totalSalesToday,
            'totalTransactions' => $totalTransactions,
            'membersRegistered' => $membersRegistered,
            'profitToday' => $profitToday,
            'topSellingProducts' => $topSellingProducts,

            'dailyProfitLabels' => $dailyData['labels'],
            'dailyProfits' => $dailyData['profits'],

            'weeklyProfitLabels' => $weeklyData['labels'],
            'weeklyProfits' => $weeklyData['profits'],

            'monthlyProfitLabels' => $monthlyData['labels'],
            'monthlyProfits' => $monthlyData['profits'],
        ]);
    }

    private function getStatsByRange($range)
    {
        $now = Carbon::now();

        switch ($range) {
            case 'daily':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'weekly':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'monthly':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'yearly':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            default:
                return ['sales' => 0, 'profit' => 0];
        }

        $transactions = Transaction::with('transactionDetails.drug')
            ->whereBetween('transaction_date', [$start, $end])
            ->where('status', Transaction::STATUS_PAID)
            ->get();

        $totalSales = $transactions->sum('total');
        $totalProfit = 0;

        foreach ($transactions as $trx) {
            foreach ($trx->transactionDetails as $detail) {
                $cost = $detail->drug->cost_price ?? 0;
                $profitPerItem = $detail->price - $cost;
                $totalProfit += $profitPerItem * $detail->quantity;
            }
        }

        return [
            'sales' => $totalSales,
            'profit' => $totalProfit
        ];
    }

    private function getChartData($range)
    {
        $now = Carbon::now();
        $format = 'Y-m-d'; // default daily

        switch ($range) {
            case 'weekly':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $format = 'l'; // Hari (Senin, Selasa, dll)
                break;
            case 'monthly':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $format = 'd M'; // Tanggal
                break;
            case 'daily':
            default:
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $format = 'H:i'; // Jam
                break;
        }

        $transactions = Transaction::with('transactionDetails.drug')
            ->whereBetween('transaction_date', [$start, $end])
            ->where('status', Transaction::STATUS_PAID)
            ->get();

        $data = [];

        foreach ($transactions as $trx) {
            $groupKey = $trx->transaction_date->format($format);
            $data[$groupKey]['sales'] = ($data[$groupKey]['sales'] ?? 0) + $trx->total;
            $data[$groupKey]['profit'] = $data[$groupKey]['profit'] ?? 0;

            foreach ($trx->transactionDetails as $detail) {
                $cost = $detail->drug->cost_price ?? 0;
                $profit = ($detail->price - $cost) * $detail->quantity;
                $data[$groupKey]['profit'] += $profit;
            }
        }

        // Urutkan berdasarkan label
        ksort($data);

        return [
            'labels' => array_keys($data),
            'sales' => array_column($data, 'sales'),
            'profits' => array_column($data, 'profit'),
        ];
    }
}
