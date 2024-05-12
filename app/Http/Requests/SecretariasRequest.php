<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SecretariasRequest extends FormRequest
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
        return [[
            "Organizacao" => ['unique','max:100','string','unique','required'],
            "Email" => ['email','unique','max:50','required'],
            "CEP" => ['string','required','max:9','min:9'],
            'UF' => ['string','required','max:2','min:2'],
            'Cidade' => ['string','required','max:30'],
            'Rua' => ['string','required'],
            'Bairro' => ['string','required']
        ],[
            'Organizacao.unique' => "Já Existe uma Organização com esse Nome!",
            'Email.unique' => "Já Existe uma Organizaçao com esse Email!"
        ]];
    }
}
