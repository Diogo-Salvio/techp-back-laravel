<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musica extends Model
{
    use HasFactory;

    protected $table = 'musicas';
    
    protected $fillable = [
        'titulo',
        'artista',
        'letra',
        'youtube_url',
        'visualizacoes',
        'posicao_top5',
        'status',
        'comentario_sugestao'
    ];

    protected $casts = [
        'visualizacoes' => 'integer',
        'posicao_top5' => 'integer',
    ];


    public function usuario()
    {
        return $this->belongsTo(User::class);
    }


    public function scopeAprovadas($query)
    {
        return $query->where('status', 'aprovada');
    }


    public function scopeTop5($query)
    {
        return $query->whereNotNull('posicao_top5')
                    ->orderBy('posicao_top5', 'asc');
    }


    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }
}
