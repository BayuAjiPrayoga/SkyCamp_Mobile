<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface KavlingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find available kavlings (not currently booked)
     */
    public function findAvailable(): Collection;

    /**
     * Find kavlings by status
     */
    public function findByStatus(string $status): Collection;

    /**
     * Get count of available kavlings
     */
    public function getAvailableCount(): int;

    /**
     * Get total count of all kavlings
     */
    public function getTotalCount(): int;
}

