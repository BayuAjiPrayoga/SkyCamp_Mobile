<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreKavlingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Auth handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:20'],
            'harga_per_malam' => ['required', 'numeric', 'min:0'],
            'fasilitas' => ['nullable', 'array'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nama' => 'nama kavling',
            'deskripsi' => 'deskripsi',
            'kapasitas' => 'kapasitas',
            'harga_per_malam' => 'harga per malam',
            'fasilitas' => 'fasilitas',
            'gambar' => 'gambar',
            'status' => 'status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kavling wajib diisi.',
            'kapasitas.required' => 'Kapasitas wajib diisi.',
            'kapasitas.min' => 'Kapasitas minimal 1 orang.',
            'harga_per_malam.required' => 'Harga per malam wajib diisi.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
