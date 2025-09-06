<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('musicas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('artista')->default('Tião Carreiro e Pardinho');
            $table->text('letra')->nullable();
            $table->string('youtube_url')->nullable();
            $table->integer('visualizacoes')->default(0);
            $table->integer('posicao_top5')->nullable();
            $table->enum('status', ['aprovada', 'pendente', 'reprovada'])->default('pendente');
            $table->text('comentario_sugestao')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('posicao_top5');
            $table->index('visualizacoes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musicas');
    }
};
