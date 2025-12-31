<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
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
            'kavling_id' => ['required', 'exists:kavlings,id'],
            'tanggal_check_in' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_check_out' => ['required', 'date', 'after:tanggal_check_in'],
            'items' => ['nullable', 'array'],
            'items.*.peralatan_id' => ['required_with:items', 'exists:peralatans,id'],
            'items.*.qty' => ['required_with:items', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kavling_id.required' => 'Kavling wajib dipilih.',
            'kavling_id.exists' => 'Kavling tidak ditemukan.',
            'tanggal_check_in.required' => 'Tanggal check-in wajib diisi.',
            'tanggal_check_in.after_or_equal' => 'Tanggal check-in minimal hari ini.',
            'tanggal_check_out.required' => 'Tanggal check-out wajib diisi.',
            'tanggal_check_out.after' => 'Tanggal check-out harus setelah check-in.',
        ];
    }
}
