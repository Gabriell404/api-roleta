<?php

namespace App\Http\Controllers;

use App\Http\Requests\Premio\PremioCreateRequest;
use App\Models\Premio;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PremioController extends Controller
{
    private $premios;

    public function __construct(Premio $premios)
    {
        $this->premios = $premios;
    }

    public function index(Request $request)
    {
        try {
            $query = $this->premios
                ->when($request->get('nomePremio'), function ($query) use ($request) {
                    return $query->where('nomePremio', 'LIKE', '%' . $request->get('nomePremio') . '%');
                })
                ->when($request->get('status'), function ($query) use ($request) {
                    return $query->where('status', '=', $request->get('status'));
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

    public function create(PremioCreateRequest $request)
    {
        try {
            if ($request->file('fileImagemPremio')) {
                $file = $request->file('fileImagemPremio');
                $fileName = $file->getClientOriginalName();
                $path = hash('sha256', time());

                if (Storage::disk('uploadsImagemPremio')->put($path . '/' . $fileName, File::get($file))) {
                    $input['nomePremio'] = $request->get('nomePremio');
                    $input['codigoColor'] = $request->get('codigoColor');
                    $input['caminhoImage'] = $path;
                    $input['regraContemplacao'] = $request->get('regraContemplacao');
                    $input['pesoPremio'] = $request->get('pesoPremio');
                    $input['estoque'] = $request->get('estoque');

                    $query = $this->premios::create($input);
                }
                return response()->json([
                    'erro' => false,
                    'mensagem' => 'Premio criado com sucesso',
                    'premio' => $query
                ], 201);

            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function getPremiosRoleta()
    {
        try {
            $query = $this->premios->where('status', '=', 'ativo')->get();

            if (count($query) == 7) {
                return response()->json($query, 200);
            } else {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'Para a roleta, é necessário que haja pelo menos 7 prêmios com status ativo.',
                    'quantidadeDeStatusAtivo' => count($query)
                ], 500);
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $premio = $this->premios::find($id);

            if ($premio == null) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O ID fornecido não pertence a nenhum prêmio.'
                ], 500);
            } else {
                $premio->update($request->all());

                return response()->json([
                    'erro' => false,
                    'mensagem' => 'Prêmio atualizado com sucesso.',
                    'premio' => $premio
                ], 200);
            }

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function udapteStatus(Request $request, int $id)
    {
        try {
            $premio = $this->premios::find($id);
            $premioAtivo = $this->premios->where('status', '=', 'ativo')->get();

            if ($premio == null) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O ID fornecido não pertence a nenhum prêmio.'
                ], 500);
            }

            if (count($premioAtivo) == 7) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O limite máximo de prêmios com o status ativo foi alcançado.',
                    'quantidadeDeStatusAtivo' => count($premioAtivo)
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

    public function delete(int $id)
    {
        try {
            $premio = $this->premios::find($id);

            if ($premio == null) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O ID fornecido não pertence a nenhum prêmio.'
                ], 500);
            } else {
                $premio->delete();

                return response()->json([
                    'erro' => false,
                    'mensagem' => 'Item excluido com sucesso.',
                    'itemExcluido' => $premio
                ], 200);
            }

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
