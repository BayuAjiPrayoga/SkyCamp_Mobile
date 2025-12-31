<?php

namespace App\Repositories\Eloquent;

use App\Models\Kavling;
use App\Models\Booking;
use App\Repositories\Contracts\KavlingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class KavlingRepository extends BaseRepository implements KavlingRepositoryInterface
{
    public function __construct(Kavling $model)
    {
        parent::__construct($model);
    }

    /**
     * Find available kavlings (not currently booked)
     */
    public function findAvailable(): Collection
    {
        $bookedIds = Booking::whereIn('status', ['pending', 'confirmed'])
            ->whereDate('tanggal_check_out', '>=', today())
            ->pluck('kavling_id');

        return $this->query()
            ->where('status', 'aktif')
            ->whereNotIn('id', $bookedIds)
            ->get();
    }

    /**
     * Find kavlings by status
     */
    public function findByStatus(string $status): Collection
    {
        return $this->findBy('status', $status);
    }

    /**
     * Get count of available kavlings
     */
    public function getAvailableCount(): int
    {
        return $this->findAvailable()->count();
    }

    /**
     * Get total count of all kavlings
     */
    public function getTotalCount(): int
    {
        return $this->query()->count();
    }
}
