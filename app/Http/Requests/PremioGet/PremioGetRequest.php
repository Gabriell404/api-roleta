<?php
namespace App\Http\Requests\PremioGet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PremioGetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'idParticipante' => 'required',
            'idEstabelecimento' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'idParticipante.required' => 'idParticipante não pode ser vazio.',
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