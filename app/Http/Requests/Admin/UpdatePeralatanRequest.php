<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePeralatanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'kategori' => ['required', Rule::in(['tenda', 'masak', 'tidur', 'lainnya'])],
            'harga_sewa' => ['required', 'numeric', 'min:0'],
            'stok_total' => ['required', 'integer', 'min:0'],
            'kondisi' => ['required', Rule::in(['baik', 'perlu_perbaikan', 'rusak'])],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
