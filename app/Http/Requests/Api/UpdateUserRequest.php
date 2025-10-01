<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
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
            'name' => 'sometimes|string|max:25',
            'bio' => 'sometimes|string|max:500',
            'location' => 'sometimes|string|max:150',
            'website' => 'sometimes|url|max:200',
            'profession' => 'sometimes|string|max:200',
            'facebook' => 'sometimes|url|max:200',
            'twitter' => 'sometimes|url|max:200',
            'instagram' => 'sometimes|url|max:200',
            'youtube' => 'sometimes|url|max:200',
            'tiktok' => 'sometimes|url|max:200',
            'pinterest' => 'sometimes|url|max:200',
            'language' => 'sometimes|string|max:10',
            'dark_mode' => 'sometimes|in:on,off',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'code' => 'VALIDATION_ERROR'
            ], 422)
        );
    }
}

