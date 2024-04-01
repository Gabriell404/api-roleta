<?php

namespace App\Http\Requests\Estabelecimento;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EstabelecimentoCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nomeEstabelecimento' => 'required|min:2|max:255',
            'cidade' => 'required|min:2|max:255',
            'estado' => 'required|min:2|max:2',
        ];
    }

    public function messages(): array
    {
        return [
            'nomeEstabelecimento.required' => 'O campo nomeEstabelecimento não pode ser vazio.',
            'nomeEstabelecimento.max' => 'O campo nomeEstabelecimento deve conter no máximo :max caracteres',
            'nomeEstabelecimento.min' => 'O campo nomeEstabelecimento deve conter no minimo :min caracteres',
            'cidade.required' => 'O campo cidade não pode ser vazio.',
            'cidade.max' => 'O campo cidade deve conter no máximo :max caracteres',
            'cidade.min' => 'O campo cidade deve conter no minimo :min caracteres',
            'estado.required' => 'O campo cidade não pode ser vazio',
            'estado.max' => 'O campo estado deve conter no máximo :max caracteres',
            'estado.min' => 'O campo estado deve conter no minimo :min caracteres'
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