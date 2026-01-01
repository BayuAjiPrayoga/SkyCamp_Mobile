<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'peralatan_id' => $this->peralatan_id,
            'peralatan' => new PeralatanResource($this->whenLoaded('peralatan')),
            'jumlah' => $this->jumlah,
            'harga' => $this->harga_sewa,
            'subtotal' => $this->subtotal,
            'subtotal_formatted' => 'Rp ' . number_format($this->subtotal, 0, ',', '.'),
        ];
    }
}
