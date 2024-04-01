<?php
namespace App\Http\Requests\Promotor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PromotorCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cpf' => 'required|min:11|max:11',
            'nome' => 'required|min:2|max:255',
            'dataAcaoPromocional' => 'required',
            'idEstabelecimento' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.required' => 'cpf não pode ser vazio',
            'cpf.min' => 'cpf deve conter no minimo :min caracteres.',
            'cpf.max' => 'cpf deve conter no máximo :max caracteres.',
            'nome.required' => 'nome não pode ser vazio.',
            'nome.min' => 'nome deve conter no minimo :min caracteres',
            'nome.max' => 'nome deve conter no máximo :max caracteres',
            'dataAcaoPromocional.required' => 'dataAcaoPromocional não pode ser vazio.',
            'idEstabelecimento.required' => 'idEstabelecimento não pode ser vazio.'
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