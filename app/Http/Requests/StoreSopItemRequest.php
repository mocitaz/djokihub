<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSopItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Ganti dengan logika otorisasi Anda yang sebenarnya
        return true; // auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'sop_section_id' => 'required|exists:sop_sections,id',
            'title' => 'required|string|max:255',
            // Pertimbangkan aturan unik jika judul item SOP harus unik dalam satu seksi
            // 'title' => ['required', 'string', 'max:255', Rule::unique('sop_items')->where(function ($query) {
            //     return $query->where('sop_section_id', $this->sop_section_id);
            // })],
            'description' => 'required|string|max:10000', // Sesuaikan max length
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'sop_section_id.required' => 'SOP section is required.',
            'sop_section_id.exists' => 'The selected SOP section does not exist.',
            'title.required' => 'The SOP item title is required.',
            // 'title.unique' => 'An SOP item with this title already exists in this section.',
            'description.required' => 'The SOP item description is required.',
        ];
    }
}
