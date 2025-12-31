<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKavlingRequest extends FormRequest
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
            'kapasitas' => ['required', 'integer', 'min:1', 'max:20'],
            'harga_per_malam' => ['required', 'numeric', 'min:0'],
            'fasilitas' => ['nullable', 'array'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
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
            'harga_per_malam.required' => 'Harga per malam wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ];
    }
}
