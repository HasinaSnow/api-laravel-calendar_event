<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterJournalRequest extends FormRequest
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
            'wording' => 'required|string',
            'date' => 'required|date',
            'debit' => 'required|boolean',
            'amount' => 'required|integer',
            'journal_type' => 'required|in:budget,equipement',
            'budget_id' => 'required_if:journal_type,budget|exists:budgets,id',
            'equipement_id' => 'required_if:journal_type,equipement|exists:equipements,id',
            'money_id' => 'required|integer|exists:money,id'
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
