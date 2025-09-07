<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SugestaoMusica extends Model
{
    use HasFactory;

    protected $table = 'sugestoes_musicas';

    protected $fillable = [
        'titulo',
        'artista',
        'youtube_url',
        'visualizacoes',
        'status',
        'user_id'
    ];

    protected $casts = [
        'visualizacoes' => 'integer',
    ];

    // Relacionamento com usuário que sugeriu
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope para sugestões pendentes
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    // Scope para sugestões aprovadas
    public function scopeAprovadas($query)
    {
        return $query->where('status', 'aprovada');
    }

    // Scope para sugestões reprovadas
    public function scopeReprovadas($query)
    {
        return $query->where('status', 'reprovada');
    }

    // Método para extrair ID do YouTube
    public function getYoutubeIdAttribute()
    {
        $url = $this->youtube_url;

        // Extrair ID do YouTube de diferentes formatos de URL
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    // Método para gerar URL de embed do YouTube
    public function getYoutubeEmbedUrlAttribute()
    {
        $id = $this->youtube_id;
        return $id ? "https://www.youtube.com/embed/{$id}" : null;
    }
}
