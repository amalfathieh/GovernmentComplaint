<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComplaintRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => [ 'string'],
            'location' => ['string', 'max:500'],
            'attachments.*' => ['nullable', 'file', 'max:4096'], // 4MB لكل ملف
            'version_number' => ['integer', 'required']
        ];
    }
}
