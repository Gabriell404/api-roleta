<?php

namespace App\Http\Controllers;

use App\Http\Requests\Premio\PremioCreateRequest;
use App\Models\Estabelecimento;
use App\Models\HistoricoContemplados;
use App\Models\Premio;
use App\Models\UltimosPremios;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Undefined;

class PremioController extends Controller
{
    private $premios;
    private $historico;
    private $estabelecimento;
    private $ultimosPremios;

    public function __construct(
        Premio $premios,
        HistoricoContemplados $historico,
        Estabelecimento $estabelecimento,
        UltimosPremios $ultimosPremios
    ) {
        $this->premios = $premios;
        $this->historico = $historico;
        $this->estabelecimento = $estabelecimento;
        $this->ultimosPremios = $ultimosPremios;
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

                    return $query->paginate(5);
                }, function ($query) {
                    return $query->get();
                });

            return response()->json($query);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function find($id) {
        return response()->json($this->premios::find($id));
    }

    public function create(PremioCreateRequest $request)
    {
        try {
            if ($request->file('fileImagemPremio')) {
                $file = $request->file('fileImagemPremio');
                $fileName = $file->getClientOriginalName();

                if (Storage::disk('public')->put($fileName, File::get($file))) {
                    $input['nomePremio'] = $request->get('nomePremio');
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

    public function createHistoricoContemplados($premioSorteado)
    {
        $this->historico->create([
            'pesoPremio' => $premioSorteado->pesoPremio,
            'idParticipante' => null,
            'idPremioContemplado' => $premioSorteado->id,
            'idEstabelecimento' => 1,
        ]);

        $premio = $this->premios::find($premioSorteado->id);
        $premio->update(['estoque' => $premioSorteado->estoque - 1]);
    }

    public function openRare()
    {
        return $this->historico->count();
    }

    public function sortePremio()
    {
        if ($this->historico->count() === 1000) {
            DB::table('historico_contemplados')->delete();
        }

        $isValid = false;

        while (!$isValid) {
            $premioRandom = DB::table('premios')->where('status', '=', 'ativo')->where('estoque', '>', 0)->inRandomOrder()->first();
            $qtdSorteadoPorPeso = $this->historico->where('pesoPremio', '=', $premioRandom->pesoPremio)->count();

            switch ($premioRandom->pesoPremio) {
                case 1:
                    if ($qtdSorteadoPorPeso < 800) {
                        $this->createHistoricoContemplados($premioRandom);
                        $isValid = true;
                    }
                    break;
                case 2:
                    if ($qtdSorteadoPorPeso < 100) {
                        $this->createHistoricoContemplados($premioRandom);
                        $isValid = true;
                    }
                    break;
                case 3:
                    if ($qtdSorteadoPorPeso < 70) {
                        $this->createHistoricoContemplados($premioRandom);
                        $isValid = true;
                    }
                    break;
                case 4:
                    if ($qtdSorteadoPorPeso < 29 && $this->openRare() >= 50) {
                        $this->createHistoricoContemplados($premioRandom);
                        $isValid = true;
                    }
                    break;
                case 5:
                    if ($qtdSorteadoPorPeso < 1 && $this->openRare() >= 100) {
                        $this->createHistoricoContemplados($premioRandom);
                        $isValid = true;
                    }
                    break;
            }
        }


        return $premioRandom;
    }

    public function getPremiosRoleta(Request $request)
    {
        try {
            if ($this->estabelecimento->where('status', '=', 'ativo')->count() == 0) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'Para utilizar a roleta é necessário que tenha um estabelecimento ativo.'
                ]);
            }

            if ($this->premios->where('status', '=', 'ativo')->where('estoque', '>', 0)->count() !== 14) {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'Para utilizar a roleta é necessário que tenha apenas 14 premios ativos com estoque',
                    'premiosAtivosComEstoque' => $this->premios->where('status', '=', 'ativo')->where('estoque', '>', 0)->count()
                ]);
            }

            $premioSorteado = $this->sortePremio();
            $premiosAtivos = $this->premios->where('status', '=', 'ativo')->take(14)->get();

            return response()->json([
                'erro' => false,
                'idDoPremioSorteado' => $premioSorteado->id,
                'premioSorteado' => $premioSorteado,
                'premiosRoleta' => $premiosAtivos
            ]);

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    // public function getPremiosRoleta() {
    //     $premiosAtivos = $this->premios->where('status', '=', 'ativo')->take(15)->get();

    //     if (count($premiosAtivos) < 15) {
    //         return response()->json([
    //             'erro' => true,
    //             'mensagem' => 'Para a roleta, é necessário que haja pelo menos 14 prêmios com status ativo.',
    //             'quantidadeDeStatusAtivo' => count($premiosAtivos)
    //         ], 500);
    //     }

    //     return response()->json([
    //         'erro' => false,
    //         'premiosRoleta' => $premiosAtivos,
    //     ]);
    // }

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
                $premio->update([
                    'nomePremio' => $request->get('nomePremio'),
                    'pesoPremio' => $request->get('pesoPremio'),
                    'estoque' => $request->get('estoque')
                ]);

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
                ]);
            }

            if (count($premioAtivo) == 14 && $request->get('status') == 'ativo') {
                return response()->json([
                    'erro' => true,
                    'mensagem' => 'O limite máximo de prêmios com o status ativo foi alcançado.',
                    'quantidadeDeStatusAtivo' => count($premioAtivo)
                ]);
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
