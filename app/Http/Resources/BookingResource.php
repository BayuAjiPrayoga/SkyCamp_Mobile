<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'tanggal_check_in' => $this->tanggal_check_in?->format('Y-m-d'),
            'tanggal_check_out' => $this->tanggal_check_out?->format('Y-m-d'),
            'total_harga' => $this->total_harga,
            'total_formatted' => 'Rp ' . number_format($this->total_harga, 0, ',', '.'),
            'bukti_pembayaran' => $this->bukti_pembayaran
                ? asset('storage/' . $this->bukti_pembayaran)
                : null,
            'qr_code' => $this->qr_code
                ? asset('storage/' . $this->qr_code)
                : null,
            'rejection_reason' => $this->rejection_reason,
            'kavling' => new KavlingResource($this->whenLoaded('kavling')),
            'user' => new UserResource($this->whenLoaded('user')),
            'items' => BookingItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get human-readable status label
     */
    protected function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'confirmed' => 'Terkonfirmasi',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
