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

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeAprovadas($query)
    {
        return $query->where('status', 'aprovada');
    }

    public function scopeReprovadas($query)
    {
        return $query->where('status', 'reprovada');
    }

    public function getYoutubeIdAttribute()
    {
        $url = $this->youtube_url;

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getYoutubeEmbedUrlAttribute()
    {
        $id = $this->youtube_id;
        return $id ? "https://www.youtube.com/embed/{$id}" : null;
    }
}
