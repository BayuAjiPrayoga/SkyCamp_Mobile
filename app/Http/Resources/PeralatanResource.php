<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeralatanResource extends JsonResource
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
            'kategori' => $this->kategori,
            'harga_sewa' => $this->harga_sewa,
            'harga_formatted' => 'Rp ' . number_format($this->harga_sewa, 0, ',', '.'),
            'stok_tersedia' => $this->stok_tersedia,
            'stok_total' => $this->stok_total,
            'kondisi' => $this->kondisi,
            'kondisi_label' => $this->getKondisiLabel(),
            'gambar' => $this->gambar ? asset('storage/' . $this->gambar) : null,
            'is_available' => $this->stok_tersedia > 0 && $this->kondisi === 'baik',
        ];
    }

    /**
     * Get human-readable kondisi label
     */
    protected function getKondisiLabel(): string
    {
        return match ($this->kondisi) {
            'baik' => 'Baik',
            'perlu_perbaikan' => 'Perlu Perbaikan',
            'rusak' => 'Rusak',
            default => ucfirst($this->kondisi),
        };
    }
}
