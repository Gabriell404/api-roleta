<?php

namespace App\Http\Controllers;

use App\Models\HistoricoContemplados;
use Illuminate\Http\Request;

class HistoricoContempladosController extends Controller
{
    private $historicoContemplados;

    public function __construct(HistoricoContemplados $historicoContemplados)
    {
        $this->historicoContemplados = $historicoContemplados;
    }

    public function index()
    {
        try {
            $query = $this->historicoContemplados->paginate(10);

            return response()->json($query, 200);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
