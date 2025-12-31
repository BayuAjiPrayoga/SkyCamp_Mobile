<?php

namespace App\Repositories\Eloquent;

use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }

    /**
     * Find bookings by user ID
     */
    public function findByUser(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->with(['kavling', 'items.peralatan'])
            ->latest()
            ->get();
    }

    /**
     * Find pending bookings with payment proof
     */
    public function findPendingWithPayment(): Collection
    {
        return $this->query()
            ->where('status', 'pending')
            ->whereNotNull('bukti_pembayaran')
            ->with(['user', 'kavling'])
            ->latest()
            ->get();
    }

    /**
     * Find bookings by status
     */
    public function findByStatus(string $status): Collection
    {
        return $this->query()
            ->where('status', $status)
            ->with(['user', 'kavling'])
            ->latest()
            ->get();
    }

    /**
     * Update booking status
     */
    public function updateStatus(int $id, string $status, ?string $reason = null): bool
    {
        $data = ['status' => $status];

        if ($reason !== null) {
            $data['rejection_reason'] = $reason;
        }

        return $this->update($id, $data);
    }

    /**
     * Get today's booking count
     */
    public function getTodayCount(): int
    {
        return $this->query()
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Get monthly revenue (confirmed bookings only)
     */
    public function getMonthlyRevenue(int $month, int $year): float
    {
        return (float) $this->query()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'confirmed')
            ->sum('total_harga');
    }

    /**
     * Get currently booked kavling IDs
     */
    public function getBookedKavlingIds(): \Illuminate\Support\Collection
    {
        return $this->query()
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('tanggal_check_out', '>=', today())
            ->pluck('kavling_id');
    }
}
