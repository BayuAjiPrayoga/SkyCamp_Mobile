<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

interface BookingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find bookings by user ID
     */
    public function findByUser(int $userId): Collection;

    /**
     * Find pending bookings with payment proof
     */
    public function findPendingWithPayment(): Collection;

    /**
     * Find bookings by status
     */
    public function findByStatus(string $status): Collection;

    /**
     * Update booking status
     */
    public function updateStatus(int $id, string $status, ?string $reason = null): bool;

    /**
     * Get today's booking count
     */
    public function getTodayCount(): int;

    /**
     * Get monthly revenue (confirmed bookings only)
     */
    public function getMonthlyRevenue(int $month, int $year): float;
}
