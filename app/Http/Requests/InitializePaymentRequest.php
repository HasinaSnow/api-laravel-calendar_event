<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class InitializePaymentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'remainder' => 'required|array',
            'remainder.expiration' => 'required|date',
            'remainder.infos' => 'nullable|string',
            'deposit_initialized' => 'required|boolean',
            'deposit' => 'required_if:deposit_initialized,true|array',
                'deposit.expiration' => 'required|date|nullable',
                'deposit.amount' => 'required_without_all:deposit.rate|integer',
                'deposit.rate' => 'required_without_all:deposit.amount|integer|min:0|max:100',
                'deposit.infos' => 'nullable|string'
        ];
    }

    /**
     * Get the exception's error of validation
     *
     * @param Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        // retourner l'exception sous forme de json
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => "validation's error!",
            'errorList' => $validator->errors() //array
        ]));
    }
}
