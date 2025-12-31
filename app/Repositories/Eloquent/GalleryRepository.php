<?php

namespace App\Repositories\Eloquent;

use App\Models\Gallery;
use App\Repositories\Contracts\GalleryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GalleryRepository extends BaseRepository implements GalleryRepositoryInterface
{
    public function __construct(Gallery $model)
    {
        parent::__construct($model);
    }

    /**
     * Find pending photos awaiting moderation
     */
    public function findPending(): Collection
    {
        return $this->query()
            ->where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Find approved photos
     */
    public function findApproved(): Collection
    {
        return $this->query()
            ->where('status', 'approved')
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Approve a photo
     */
    public function approve(int $id): bool
    {
        return $this->update($id, ['status' => 'approved']);
    }

    /**
     * Reject a photo
     */
    public function reject(int $id): bool
    {
        return $this->update($id, ['status' => 'rejected']);
    }

    /**
     * Get pending count
     */
    public function getPendingCount(): int
    {
        return $this->query()->where('status', 'pending')->count();
    }

    /**
     * Get total count
     */
    public function getTotalCount(): int
    {
        return $this->query()->count();
    }
}
