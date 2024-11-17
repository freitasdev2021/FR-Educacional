<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use App\Rules\PasswordRule;

class CandidatoRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'Telefone' => ['unique:candidatos']
        ];
    }

    // Personalize as mensagens de erro
    public function messages()
    {
        return [
            'name.required' => 'O nome é obrigatório. Por favor, preencha este campo.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'As senhas não correspondem.',
            'Telefone.unique' => 'Telefone Já em Uso'
        ];
    }
}
