<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubscribeRequest extends FormRequest
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
            'creator_id' => 'required|integer|exists:users,id',
            'interval' => 'required|in:monthly,quarterly,biannually,yearly',
            'payment_gateway' => 'sometimes|string|in:wallet,stripe,paypal,ccbill,kkiapay',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'creator_id.required' => 'Creator ID is required',
            'creator_id.exists' => 'Creator not found',
            'interval.required' => 'Subscription interval is required',
            'interval.in' => 'Invalid subscription interval',
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

