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
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Revenue breakdown for selected range
        $weeklyData = $this->getRevenueData($startDate, $endDate);

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
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export revenue report as PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $weeklyData = $this->getRevenueData($startDate, $endDate);
        $totalRevenue = collect($weeklyData)->sum('revenue');
        $totalBookings = collect($weeklyData)->sum('bookings');

        $rangeString = \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y');

        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'weeklyData',
            'totalRevenue',
            'totalBookings',
            'rangeString',
            'startDate',
            'endDate'
        ));

        $fileName = 'laporan-pendapatan-' . str_replace(' ', '-', $rangeString) . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Export inventory as Excel
     */
    public function exportExcel()
    {
        return Excel::download(new PeralatanExport, 'inventaris-peralatan.xlsx');
    }

    /**
     * Get revenue breakdown by weeks/periods within range
     */
    private function getRevenueData(string $startDate, string $endDate): array
    {
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();
        $weeks = [];

        $current = $start->copy();
        $weekCounter = 1;

        while ($current->lte($end)) {
            // Define chunk: 7 days or until end of range
            $chunkEnd = $current->copy()->addDays(6)->endOfDay();
            if ($chunkEnd->gt($end)) {
                $chunkEnd = $end->copy(); // Cap last chunk at end date
            }

            $bookings = Booking::whereBetween('created_at', [$current, $chunkEnd])
                ->where('status', 'confirmed')
                ->get();

            $weeks[] = [
                'week' => $weekCounter,
                'start' => $current->translatedFormat('d M'),
                'end' => $chunkEnd->translatedFormat('d M'),
                'revenue' => $bookings->sum('total_harga'),
                'bookings' => $bookings->count(),
            ];

            $current->addDays(7);
            $weekCounter++;
        }

        return $weeks;
    }
}
