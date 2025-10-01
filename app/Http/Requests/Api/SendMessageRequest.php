<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendMessageRequest extends FormRequest
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
            'to_user_id' => 'required|integer|exists:users,id',
            'message' => 'required_without:media|string|max:5000',
            'price' => 'sometimes|numeric|min:0',
            'tip' => 'sometimes|in:yes,no',
            'tip_amount' => 'required_if:tip,yes|numeric|min:0',
            'media' => 'sometimes|array|max:10',
            'media.*.type' => 'required|in:image,video,audio,file',
            'media.*.file' => 'required|string',
            'media.*.token' => 'sometimes|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'to_user_id.required' => 'Recipient is required',
            'to_user_id.exists' => 'Recipient not found',
            'message.required_without' => 'Message or media is required',
            'tip_amount.required_if' => 'Tip amount is required when sending a tip',
            'media.max' => 'Maximum 10 media items allowed',
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

