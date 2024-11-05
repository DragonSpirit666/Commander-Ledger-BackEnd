<?php

namespace App\Http\Requests;

use App\Models\Utilisateur;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UtilisateurRequest extends FormRequest
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
            'nom' => ['required', 'string', 'max:255'],
            'couriel' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Utilisateur::class],
            'mot_de_passe' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
