<?php

namespace App\Http\Controllers;

use App\Http\Requests\Promotor\PromotorCreateRequest;
use App\Models\Promotor;
use Illuminate\Http\Request;

class PromotorController extends Controller
{
    private $promotores;

    public function __construct(Promotor $promotores)
    {
        $this->promotores = $promotores;
    }

    public function create(PromotorCreateRequest $request)
    {
        try {
            $query = $this->promotores->create([
                'cpf' => $request->get('cpf'),
                'nome' => $request->get('nome'),
                'dataAcaoPromocional' => $request->get('dataAcaoPromocional'),
                'idEstabelecimento' => $request->get('idEstabelecimento')
            ]);

            return response()->json([
                'erro' => false,
                'mensagem' => 'Promotor criado com sucesso.',
                'promotor' => $query
            ], 201);

        } catch (\Throwable $th) {
            return $th->getMessage();
        }

    }

    public function index(Request $request)
    {
        try {
            $query = $this->promotores
                ->when($request->get('cpf'), function ($query) use ($request) {
                    return $query->where('cpf', '=', $request->get('cpf'));
                })
                ->when($request->get('nome'), function ($query) use ($request) {
                    return $query->where('nome', 'LIKE', '%' . $request->get('nome') . '%');
                })
                ->when($request->get('page'), function ($query) use ($request) {
                    if ($request->get('page') < 0) {
                        return $query->get();
                    }

                    return $query->paginate(10);
                }, function ($query) {
                    return $query->get();
                });

            return response()->json($query);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function delete(int $id)
    {
        try {
            $promotor = $this->promotores::find($id);

            if ($promotor == null) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O ID fornecido não pertence a nenhum promotor.'
                ], 500);
            } else {
                $promotor->delete();

                return response()->json([
                    'erro' => false,
                    'mensagem' => 'Item excluido com sucesso.',
                    'itemExcluido' => $promotor
                ], 200);
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $promotor = $this->promotores::find($id);

            if ($promotor == null) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O ID fornecido não pertence a nenhum promotor.'
                ], 500);
            }else {
                $promotor->update($request->all());
            }

            return response()->json([
                'erro' => false,
                'mensagem' => 'Promotor atualizado com sucesso.',
                'promotor' => $promotor
            ], 200);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
