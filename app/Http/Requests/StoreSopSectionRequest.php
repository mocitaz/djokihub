<?php

namespace App\Http\Requests; // Pastikan namespace ini benar

use Illuminate\Foundation\Http\FormRequest;

class StoreSopSectionRequest extends FormRequest // Pastikan nama kelas ini benar
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ganti dengan logika otorisasi Anda jika perlu
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:sop_sections,title',
            'introduction' => 'nullable|string|max:5000',
        ];
    }
}