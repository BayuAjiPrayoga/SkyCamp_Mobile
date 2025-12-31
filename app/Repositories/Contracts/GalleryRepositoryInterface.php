<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface GalleryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find pending photos awaiting moderation
     */
    public function findPending(): Collection;

    /**
     * Find approved photos
     */
    public function findApproved(): Collection;

    /**
     * Approve a photo
     */
    public function approve(int $id): bool;

    /**
     * Reject a photo
     */
    public function reject(int $id): bool;

    /**
     * Get pending count
     */
    public function getPendingCount(): int;

    /**
     * Get total count
     */
    public function getTotalCount(): int;
}

