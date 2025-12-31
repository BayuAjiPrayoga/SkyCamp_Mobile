<?php

namespace App\Exports;

use App\Models\Announcement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class PengumumanExport implements FromQuery, WithHeadings, WithMapping
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
        $query = Announcement::query();

        // Apply search filter
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Apply type filter
        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        // Apply status filter
        if (isset($this->filters['is_active']) && $this->filters['is_active'] !== '') {
            $query->where('is_active', (bool) $this->filters['is_active']);
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
            'Judul',
            'Isi Pengumuman',
            'Tipe',
            'Status',
            'Dibuat Pada',
        ];
    }

    /**
     * Map each row
     */
    public function map($announcement): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $announcement->title,
            $announcement->content,
            $this->formatType($announcement->type),
            $announcement->is_active ? 'Aktif' : 'Tidak Aktif',
            $announcement->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Format type for display
     */
    private function formatType(string $type): string
    {
        return match ($type) {
            'info' => 'Info',
            'warning' => 'Warning',
            'success' => 'Success',
            default => ucfirst($type),
        };
    }
}
