<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KavlingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'kapasitas' => $this->kapasitas,
            'harga_per_malam' => $this->harga_per_malam,
            'harga_formatted' => 'Rp ' . number_format($this->harga_per_malam, 0, ',', '.'),
            'fasilitas' => $this->fasilitas ?? [],
            'gambar' => $this->gambar ? asset('storage/' . $this->gambar) : null,
            'status' => $this->status,
            'is_available' => $this->status === 'aktif',
        ];
    }
}
