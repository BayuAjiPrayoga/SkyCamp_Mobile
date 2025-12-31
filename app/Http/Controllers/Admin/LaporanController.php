<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Peralatan;
use App\Exports\PeralatanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Display reports page
     */
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Weekly breakdown for selected month/year
        $weeklyData = $this->getWeeklyRevenue($month, $year);

        // Equipment summary
        $totalItems = Peralatan::count();
        $goodCondition = Peralatan::where('kondisi', 'baik')->count();
        $needRepair = Peralatan::where('kondisi', 'perlu_perbaikan')->count();
        $damaged = Peralatan::where('kondisi', 'rusak')->count();

        return view('admin.laporan.index', compact(
            'weeklyData',
            'totalItems',
            'goodCondition',
            'needRepair',
            'damaged',
            'month',
            'year'
        ));
    }

    /**
     * Export revenue report as PDF
     */
    public function exportPdf(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        // Validate month and year
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }
        if ($year < 2020 || $year > 2030) {
            $year = now()->year;
        }

        $weeklyData = $this->getWeeklyRevenue($month, $year);
        $totalRevenue = collect($weeklyData)->sum('revenue');
        $totalBookings = collect($weeklyData)->sum('bookings');

        $monthName = \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F');

        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'weeklyData',
            'totalRevenue',
            'totalBookings',
            'monthName',
            'year'
        ));

        return $pdf->download("laporan-pendapatan-{$monthName}-{$year}.pdf");
    }

    /**
     * Export inventory as Excel
     */
    public function exportExcel()
    {
        return Excel::download(new PeralatanExport, 'inventaris-peralatan.xlsx');
    }

    /**
     * Get weekly revenue breakdown (limited to 4 weeks per month)
     */
    private function getWeeklyRevenue(int $month, int $year): array
    {
        $weeks = [];
        $startOfMonth = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endOfMonth = now()->setYear($year)->setMonth($month)->endOfMonth();
        $daysInMonth = $endOfMonth->day;

        // Divide month into 4 weeks
        $weekRanges = [
            ['start' => 1, 'end' => 7],
            ['start' => 8, 'end' => 14],
            ['start' => 15, 'end' => 21],
            ['start' => 22, 'end' => $daysInMonth],
        ];

        foreach ($weekRanges as $index => $range) {
            $weekStart = $startOfMonth->copy()->setDay($range['start'])->startOfDay();
            $weekEnd = $startOfMonth->copy()->setDay($range['end'])->endOfDay();

            $bookings = Booking::whereBetween('created_at', [$weekStart, $weekEnd])
                ->where('status', 'confirmed')
                ->get();

            $weeks[] = [
                'week' => $index + 1,
                'start' => $weekStart->format('d'),
                'end' => $weekEnd->format('d'),
                'revenue' => $bookings->sum('total_harga'),
                'bookings' => $bookings->count(),
            ];
        }

        return $weeks;
    }
}
