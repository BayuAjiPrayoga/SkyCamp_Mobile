<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePengumumanRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:2000'],
            'type' => ['required', 'in:info,warning,success'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul pengumuman',
            'content' => 'isi pengumuman',
            'type' => 'tipe pengumuman',
            'is_active' => 'status aktif',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'title.max' => 'Judul pengumuman maksimal 255 karakter.',
            'content.required' => 'Isi pengumuman wajib diisi.',
            'content.max' => 'Isi pengumuman maksimal 2000 karakter.',
            'type.required' => 'Tipe pengumuman wajib dipilih.',
            'type.in' => 'Tipe pengumuman harus salah satu dari: Info, Warning, atau Success.',
        ];
    }
}
