<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
        $startOfYear = $today->copy()->startOfYear();
        $endOfYear = $today->copy()->endOfYear();

        // === TOTAL LAPORAN ===
        $weekly = Report::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        $monthly = Report::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $yearly = Report::whereBetween('created_at', [$startOfYear, $endOfYear])->count();

        // === DATA HARI INI ===
        $todaySubmitted = Report::whereDate('created_at', $today)
                            ->where('status', 'submitted')
                            ->count();

        $todayDraft = Report::whereDate('created_at', $today)
                            ->where('status', 'draft')
                            ->count();

        // === DATA HARIAN (HANYA HARI YANG ADA LAPORAN) ===
        $dailyReports = Report::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = $dailyReports->pluck('date')->map(function($d){
            return Carbon::parse($d)->format('d M');
        });

        $data = $dailyReports->pluck('total');

        // === TREND HARIAN ===
        $todayCount = Report::whereDate('created_at', $today)->count();
        $yesterdayCount = Report::whereDate('created_at', $yesterday)->count();

        if ($yesterdayCount > 0) {
            $dailyTrend = round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100, 1);
        } else {
            $dailyTrend = 0;
        }

         $monthlyReports = Report::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyReports[$i] ?? 0;
        }

        // === PERIODE SEKARANG ===
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();

        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $startOfYear = $today->copy()->startOfYear();
        $endOfYear = $today->copy()->endOfYear();

        // === PERIODE SEBELUMNYA ===
        $prevWeekStart = $startOfWeek->copy()->subWeek();
        $prevWeekEnd = $endOfWeek->copy()->subWeek();

        $prevMonthStart = $startOfMonth->copy()->subMonth();
        $prevMonthEnd = $endOfMonth->copy()->subMonth();

        $prevYearStart = $startOfYear->copy()->subYear();
        $prevYearEnd = $endOfYear->copy()->subYear();

        
        // === HITUNG JUMLAH LAPORAN ===
        $weekly = Report::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        $monthly = Report::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $yearly = Report::whereBetween('created_at', [$startOfYear, $endOfYear])->count();

        $prevWeekly = Report::whereBetween('created_at', [$prevWeekStart, $prevWeekEnd])->count();
        $prevMonthly = Report::whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])->count();
        $prevYearly = Report::whereBetween('created_at', [$prevYearStart, $prevYearEnd])->count();

         // === HITUNG PERSENTASE PERUBAHAN ===
        $weekTrend = $prevWeekly > 0 ? round((($weekly - $prevWeekly) / $prevWeekly) * 100, 1) : 0;
        $monthTrend = $prevMonthly > 0 ? round((($monthly - $prevMonthly) / $prevMonthly) * 100, 1) : 0;
        $yearTrend = $prevYearly > 0 ? round((($yearly - $prevYearly) / $prevYearly) * 100, 1) : 0;

        return view('dashboard', [
            'weekly' => $weekly,
            'monthly' => $monthly,
            'yearly' => $yearly,
            'todaySubmitted' => $todaySubmitted,
            'todayDraft' => $todayDraft,
            'dailyLabels' => $labels,
            'dailyData' => $data,
            'dailyTrend' => $dailyTrend,
            'chartData' => $chartData,
            'weekTrend' => $weekTrend,
            'monthTrend' => $monthTrend,
            'yearTrend' => $yearTrend,
        ]);
    }
}
