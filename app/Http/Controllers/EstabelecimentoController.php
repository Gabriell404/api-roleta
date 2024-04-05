<?php

namespace App\Http\Controllers;

use App\Http\Requests\Estabelecimento\EstabelecimentoCreateRequest;
use App\Models\Estabelecimento;
use Illuminate\Http\Request;

class EstabelecimentoController extends Controller
{
    private $estabelecimento;

    public function __construct(Estabelecimento $estabelecimento)
    {
        $this->estabelecimento = $estabelecimento;
    }

    public function create(EstabelecimentoCreateRequest $request)
    {
        try {
            $query = $this->estabelecimento->create([
                'nomeEstabelecimento' => $request->get('nomeEstabelecimento'),
                'cidade' => $request->get('cidade'),
                'estado' => $request->get('estado'),
            ]);

            return response()->json([
                'erro' => false,
                'mensagem' => 'Estabelecimento criado com sucesso.',
                'estabelecimento' => $query
            ]);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function index(Request $request)
    {
        try {
            $query = $this->estabelecimento
                ->when($request->get('nomeEstabelecimento'), function ($query) use ($request) {
                    return $query->where('nomeEstabelecimento', 'LIKE', '%' . $request->get('nomeEstabelecimento') . '%');
                })
                ->when($request->get('cidade'), function ($query) use ($request) {
                    return $query->where('cidade', '=', $request->get('cidade'));
                })
                ->when($request->get('estado'), function ($query) use ($request) {
                    return $query->where('estado', '=', $request->get('estado'));
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

    public function update(Request $request, int $id)
    {
        try {
            $estabelecimento = $this->estabelecimento::find($id);

            if ($estabelecimento == null) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O ID fornecido não pertence a nenhum estabelecimento.'
                ], 500);
            } else {
                $estabelecimento->update($request->all());

                return response()->json([
                    'erro' => false,
                    'mensagem' => 'Estabelecimento atualizado com sucesso.',
                    'premio' => $estabelecimento
                ], 200);
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function delete(int $id)
    {
        try {
            $estabelecimento = $this->estabelecimento::find($id);

            if ($estabelecimento == null) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O ID fornecido não pertence a nenhum estabelecimento.'
                ], 500);
            } else {
                $estabelecimento->delete();

                return response()->json([
                    'erro' => false,
                    'mensagem' => 'Item excluido com sucesso.',
                    'itemExcluido' => $estabelecimento
                ], 200);
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function updateStatus(Request $request, int $id)
    {
        try {
            $premio = $this->estabelecimento::find($id);
            $estabelecimentoAtivo = $this->estabelecimento->where('status', '=', 'ativo')->get();

            if ($premio == null) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O ID fornecido não pertence a nenhum prêmio.'
                ], 500);
            }

            if (count($estabelecimentoAtivo) >= 1 && $request->get('status') == 'ativo') {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O limite máximo de prêmios com o status ativo foi alcançado.',
                    'quantidadeDeStatusAtivo' => count($estabelecimentoAtivo)
                ], 500);
            } else {
                $premio->update([
                    'status' => $request->get('status')
                ]);

                return response()->json([
                    'erro' => false,
                    'mensagem' => 'Status do prêmio atualizado com sucesso.',
                    'premio' => $premio
                ], 200);
            }
        } catch (\Throwable $th) {
            
            return $th->getMessage();
        }
    }
}
