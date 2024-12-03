<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartieRequest extends FormRequest
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
            'date' => ['required', 'date', 'date_format:Y/m/d'],
            'participants' => ['required', 'array', 'min:2', 'max:8'],
            'participants.*.deck_id' => ['required', 'integer', 'exists:decks,id'],
            'participants.*.position' => ['required', 'integer'],
        ];
    }
}
