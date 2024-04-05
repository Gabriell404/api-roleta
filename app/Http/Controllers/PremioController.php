<?php

namespace App\Http\Controllers;

use App\Http\Requests\Premio\PremioCreateRequest;
use App\Http\Requests\PremioGet\PremioGetRequest;
use App\Models\Estabelecimento;
use App\Models\HistoricoContemplados;
use App\Models\Premio;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Mockery\Undefined;

class PremioController extends Controller
{
    private $premios;
    private $historico;
    private $estabelecimento;

    public function __construct(Premio $premios, HistoricoContemplados $historico, Estabelecimento $estabelecimento)
    {
        $this->premios = $premios;
        $this->historico = $historico;
        $this->estabelecimento = $estabelecimento;
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

                if (Storage::disk('public')->put($fileName, File::get($file))) {
                    $input['nomePremio'] = $request->get('nomePremio');
                    $input['codigoColor'] = $request->get('codigoColor');
                    $input['caminhoImage'] = $fileName;
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

    public function getPremiosRoleta(Request $request)
    {
        try {
            $dataDoDiaAtual = Carbon::today()->format('Y-m-d H:i:s');
            $premiosAtivo = $this->premios->where('status', '=', 'ativo')->get();
            $somaPesos = $premiosAtivo->sum('pesoPremio');
            $chancesAcumuladas = [];
            $acumulador = 0;
            $numeroSorteado = rand(1, 100);
            $premioSorteado = $this->premios;

            if (count($premiosAtivo) == 7) {
                foreach ($premiosAtivo as $premio) {
                    $chance = ($premio->pesoPremio / $somaPesos) * 100;
                    $acumulador += $chance;
                    $chancesAcumuladas[] = $acumulador;
                }

                foreach ($chancesAcumuladas as $index => $chance) {
                    if ($numeroSorteado <= $chance) {
                        $premioSorteado = $premiosAtivo[$index];
                        break;
                    }
                }

                $idDoPremioSorteado = $premioSorteado->id;
                $resultados = $this->historico->whereDate('created_at', $dataDoDiaAtual)->where('idPremioContemplado', '=', $idDoPremioSorteado)->get();
                $count = count($resultados);
                $estoqueDoPremioSorteado = $premioSorteado->estoque;

                if ($premioSorteado) {
                    $idEstabelecimento = $this->estabelecimento->select('id')->where('status', '=', 'ativo')->pluck('id');
                    
                    if ($premioSorteado->estoque != 0) {
                        if ($estoqueDoPremioSorteado - $count > 0) {
                            $this->historico->create([
                                'pesoPremio' => $premioSorteado->pesoPremio,
                                'idParticipante' => $request->get('idParticipante'),
                                'idPremioContemplado' => $premioSorteado->id,
                                'idEstabelecimento' => $idEstabelecimento[0],
                            ]);
                        } else {
                            $premioSorteado = [];
                        }
                    } else {
                        $this->historico->create([
                            'pesoPremio' => $premioSorteado->pesoPremio,
                            'idParticipante' => $request->get('idParticipante'),
                            'idPremioContemplado' => $premioSorteado->id,
                            'idEstabelecimento' => $idEstabelecimento[0],
                        ]);
                    }
                }

                return response()->json([
                    'erro' => false,
                    'idDoPremioSorteado' => $idDoPremioSorteado,
                    'premiosRoleta' => $premiosAtivo,
                    'premioSorteado' => $premioSorteado
                ]);
            } else {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'Para a roleta, é necessário que haja pelo menos 7 prêmios com status ativo.',
                    'quantidadeDeStatusAtivo' => count($premiosAtivo)
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

    public function updateStatus(Request $request, int $id)
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

            if (count($premioAtivo) == 7 && $request->get('status') == 'ativo') {
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
