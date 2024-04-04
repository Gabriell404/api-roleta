<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UsuarioCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nome' => 'required|min:1|:max:255',
            'email' => 'required',
            'senha' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O campo nome não pode ser vazio.',
            'nome.max' => 'O campo nome deve conter no máximo :max caracteres',
            'nome.min' => 'O campo nome deve conter no minimo :min caracteres',
            'email.required' => 'O campo email não pode ser vazio.',
            'senha.required' => 'O campo senha não pode ser vazio',
        ];
    }


    public function withValidator($validator)
    {
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'mensagem' => 'Algum campo não foi preenchido de forma correta',
                'erro' => $validator->errors(),
            ], 400));
        }
    }
}
