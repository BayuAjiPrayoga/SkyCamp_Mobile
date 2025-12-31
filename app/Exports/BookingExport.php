<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Build query with filters applied
     */
    public function query()
    {
        $query = Booking::with(['user', 'kavling']);

        // Apply status filter
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Apply search filter
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        // Apply date range filter
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('tanggal_check_in', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('tanggal_check_out', '<=', $this->filters['date_to']);
        }

        return $query->latest();
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Booking',
            'Customer',
            'Email',
            'Kavling',
            'Check In',
            'Check Out',
            'Total Harga',
            'Status',
            'Dibuat Pada',
        ];
    }

    /**
     * Map each row
     */
    public function map($booking): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $booking->code,
            $booking->user->name ?? 'Guest',
            $booking->user->email ?? '-',
            $booking->kavling->nama ?? '-',
            $booking->tanggal_check_in?->format('d/m/Y') ?? '-',
            $booking->tanggal_check_out?->format('d/m/Y') ?? '-',
            'Rp ' . number_format($booking->total_harga, 0, ',', '.'),
            $this->formatStatus($booking->status),
            $booking->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Format status for display
     */
    private function formatStatus(string $status): string
    {
        return match ($status) {
            'pending' => 'Pending',
            'confirmed' => 'Dikonfirmasi',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($status),
        };
    }
}
