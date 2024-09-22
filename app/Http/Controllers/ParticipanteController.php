<?php

namespace App\Http\Controllers;

use App\Http\Requests\Participante\ParticipanteCreateRequest;
use App\Models\Estabelecimento;
use App\Models\Participante;
use Illuminate\Http\Request;

class ParticipanteController extends Controller
{
    private $participante;
    private $estabelecimento;

    public function __construct(Participante $participante, Estabelecimento $estabelecimento)
    {
        $this->participante = $participante;
        $this->estabelecimento = $estabelecimento;

    }

    public function create(ParticipanteCreateRequest $request)
    {
        try {
            $participante = $this->participante->where('telefone', $request->get('telefone'))->first();
            $estabelecimento = $this->estabelecimento->where('status', '=', 'ativo')->first();

            if ($participante) {
                return response([
                    'erro' => true,
                    'mensagem' => 'Esse participante já foi cadastrado!'
                ], 500);
            }

            $query = $this->participante->create([
                'nome' => $request->get('nome'),
                'idade' => $request->get('idade'),
                'instagram' => $request->get('instagram'),
                'telefone' => $request->get('telefone'),
                'idEstabelecimento' => $estabelecimento->id
            ]);

            return response()->json([
                'erro' => false,
                'mensagem' => 'Participante criado com sucesso.',
                'participante' => $query
            ], 201);

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function index(Request $request)
    {
        try {
            $query = $this->participante->when($request->get('nome'), function ($query) use ($request) {
                return $query->where('nome', 'LIKE', '%' . $request->get('nome') . '%');
            })
                ->when($request->get('cpf'), function ($query) use ($request) {
                    return $query->where('cpf', '=', $request->get('cpf'));
                })
                ->when($request->get('telefone'), function ($query) use ($request) {
                    return $query->where('telefone', '=', $request->get('telefone'));
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
            $participante = $this->participante::find($id);

            if ($participante == null) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O ID fornecido não pertence a nenhum participante.'
                ], 500);
            } else {
                $participante->update($request->all());

                return response()->json([
                    'erro' => false,
                    'mensagem' => 'Participante atualizado com sucesso.',
                    'premio' => $participante
                ], 200);
            }

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function delete(int $id)
    {
        try {
            $participante = $this->participante::find($id);

            if ($participante == null) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O ID fornecido não pertence a nenhum participante.'
                ], 500);
            } else {
                $participante->delete();

                return response()->json([
                    'erro' => false,
                    'mensagem' => 'Item excluido com sucesso.',
                    'itemExcluido' => $participante
                ], 200);
            }

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function find(String $telefone)
    {
        try {

            return response()->json(
                $this->participante->query()->with('estabelecimento')->where('telefone', $telefone)->orderBy('id', 'desc')->first()
            );

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
