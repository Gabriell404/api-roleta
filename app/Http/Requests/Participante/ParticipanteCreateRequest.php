<?php
namespace App\Http\Requests\Participante;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ParticipanteCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nome' => 'required|min:2|max:255',
            'idade' => 'required|max:255',
            'cpf' => 'required|min:11|max:11',
            'telefone' => 'required|min:9|max:255',
            'cupomFiscal' => 'required|max:255',
            'dataParticipacao' => 'required',
            'idEstabelecimento' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'nome não pode ser vazio.',
            'nome.min' => 'nome deve conter no minimo :min caracteres.',
            'nome.max' => 'nome deve conter no máximo :max caracteres.',
            'idade.required' => 'idade não pode ser vazio.',
            'idade.max' => 'idade deve conter no máximo :max caracteres',
            'cpf.required' => 'cpf não pode ser vazio.',
            'cpf.min' => 'cpf deve conter no minimo :min caracteres.',
            'cpf.max' => 'cpf deve conter no máximo :max caracteres.',
            'telefone.required' => 'telefone não pode ser vazio.',
            'telefone.min' => 'telefone deve conter no minimo :min caracteres',
            'telefone.max' => 'telefone deve conter no máximo :max caracteres',
            'cupomFiscal.required' => 'cupomFiscal não pode ser vazio.',
            'cupomFiscal.max' => 'cupomFiscal deve conter no máximo :max caracteres',
            'dataParticipacao.required' => 'dataParticipacao não pode ser vazio.',
            'idEstabelecimento.required' => 'idEstabelecimento não pode ser vazio.',
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