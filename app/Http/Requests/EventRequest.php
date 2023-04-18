<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EventRequest extends FormRequest
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
            // creations
            'date' => 'required|date',

            'budget_creation' => 'required|boolean',
            'budget_amount' => 'integer',
            'budget_infos' => 'string',

            'invoice_infos' => 'string',

            // selections
                // uniques
                'audience' => 'required|bool',
                'client_id' => 'required|exists:clients,id',
                'place_id' => 'required|exists:places,id',
                'category_id' => 'required|exists:categories,id',
                'type_id' => 'required|exists:types,id',
                'confirmation_id' => 'required|exists:confirmations,id',
                'pack_id' => 'required|exists:packs,id',

                // multiples
                'service_id' => 'required|array|exists:services,id'
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
