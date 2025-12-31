<?php

namespace App\Services;

use App\Repositories\Contracts\GalleryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use App\Models\Gallery;

class GalleryService
{
    public function __construct(
        protected GalleryRepositoryInterface $galleryRepository
    ) {
    }

    /**
     * Get pending photos for moderation
     */
    public function getPendingPhotos(): LengthAwarePaginator
    {
        return Gallery::where('status', 'pending')
            ->with('user')
            ->latest()
            ->paginate(15);
    }

    /**
     * Get approved photos for display
     */
    public function getApprovedPhotos(): LengthAwarePaginator
    {
        return Gallery::where('status', 'approved')
            ->with('user')
            ->latest()
            ->paginate(15);
    }

    /**
     * Approve a photo
     */
    public function approvePhoto(int $photoId): bool
    {
        return $this->galleryRepository->approve($photoId);
    }

    /**
     * Reject a photo
     */
    public function rejectPhoto(int $photoId): bool
    {
        return $this->galleryRepository->reject($photoId);
    }

    /**
     * Upload a new photo
     */
    public function uploadPhoto(UploadedFile $file, int $userId): Gallery
    {
        $path = $file->store('gallery', 'public');

        return $this->galleryRepository->create([
            'user_id' => $userId,
            'path' => $path,
            'status' => 'pending',
        ]);
    }

    /**
     * Get pending count
     */
    public function getPendingCount(): int
    {
        return $this->galleryRepository->getPendingCount();
    }

    /**
     * Get total count
     */
    public function getTotalCount(): int
    {
        return $this->galleryRepository->getTotalCount();
    }

    /**
     * Bulk approve photos
     */
    public function bulkApprove(array $photoIds): int
    {
        $count = 0;
        foreach ($photoIds as $id) {
            if ($this->galleryRepository->approve($id)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Bulk reject photos
     */
    public function bulkReject(array $photoIds): int
    {
        $count = 0;
        foreach ($photoIds as $id) {
            if ($this->galleryRepository->reject($id)) {
                $count++;
            }
        }
        return $count;
    }
}
