<?php
namespace App\Http\Requests\Premio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PremioCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nomePremio' => 'required|min:2|max:255',
            'codigoColor' => 'required|min:2|max:255',
            'regraContemplacao' => 'required|min:2|max:255',
            'pesoPremio' => 'required|min:1|max:255',
            'estoque' => 'required|min:1|max:10',
            'fileImagemPremio' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'fileImagemPremio.required' => 'fileImagemPremio não pode ser vazio',
            'nomePremio.required' => 'nomePremio não pode ser vazio.',
            'nomePremio.min' => 'nomePremio deve conter no minimo :min caracteres.',
            'nomePremio.max' => 'nomePremio deve conter no máximo :max caracteres.',
            'codigoColor.required' => 'codigoColor não pode ser vazio.',
            'codigoColor.min' => 'codigoColor deve conter no minimo :min caracteres',
            'codigoColor.max' => 'codigoColor deve conter no máximo :max caracteres',
            'regraContemplacao.required' => 'regraContemplacao não pode ser vazio.',
            'regraContemplacao.min' => 'regraContemplacao deve conter no minimo :min caracteres.',
            'regraContemplacao.max' => 'regraContemplacao deve conter no máximo :max caracteres.',
            'pesoPremio.required' => 'pesoPremio não pode ser vazio.',
            'pesoPremio.min' => 'pesoPremio deve conter no minimo :min caracteres',
            'pesoPremio.max' => 'pesoPremio deve conter no máximo :max caracteres',
            'estoque.required' => 'estoque não pode ser vazio.',
            'estoque.min' => 'estoque deve conter no minimo :min caracteres',
            'estoque.max' => 'estoque deve conter no máximo :max caracteres'
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