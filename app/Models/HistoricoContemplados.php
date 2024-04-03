<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoContemplados extends Model
{
    use HasFactory;
    protected $fillable = [
        'pesoPremio',
        'dataHoraContemplacao',
        'idParticipante',
        'idPremioContemplado',
        'idEstabelecimento'
    ];
}
