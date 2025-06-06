<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSopSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Replace with your actual authorization logic
        return true; // auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Get the ID of the SopSection being updated from the route parameter
        // The route parameter name must match what's defined in your routes file (e.g., {sopSection})
        $sopSectionId = $this->route('sopSection')->id ?? null;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sop_sections', 'title')->ignore($sopSectionId),
            ],
            'introduction' => 'nullable|string|max:5000',
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
            'title.required' => 'The SOP section title is required.',
            'title.unique' => 'An SOP section with this title already exists.',
            'introduction.max' => 'The introduction may not be greater than 5000 characters.',
        ];
    }
}
