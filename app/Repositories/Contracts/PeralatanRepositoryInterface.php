<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PeralatanRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find peralatan by category
     */
    public function findByCategory(string $category): Collection;

    /**
     * Find available peralatan (with stock)
     */
    public function findAvailable(): Collection;

    /**
     * Update stock
     */
    public function updateStock(int $id, int $stockChange): bool;

    /**
     * Get total stock count
     */
    public function getTotalStock(): int;

    /**
     * Get available stock count
     */
    public function getAvailableStock(): int;
}
