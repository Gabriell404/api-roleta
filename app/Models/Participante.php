<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome',
        'idade',
        'instagram',
        'telefone',
        'dataParticipacao',
        'idEstabelecimento'
    ];

    public function estabelecimento()
    {
        return $this->belongsTo(Estabelecimento::class, 'idEstabelecimento');
    }
}
