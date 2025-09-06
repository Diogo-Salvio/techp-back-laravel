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
        Schema::create('sugestoes_musicas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('artista')->nullable();
            $table->text('letra')->nullable();
            $table->string('youtube_url');
            $table->integer('visualizacoes')->default(0);
            $table->text('comentario_sugestao')->nullable();
            $table->enum('status', ['pendente', 'aprovada', 'reprovada'])->default('pendente');
            $table->unsignedBigInteger('user_id')->nullable(); // Quem sugeriu
            $table->timestamps();

            // Índices
            $table->index('status');
            $table->index('user_id');

            // Chave estrangeira
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sugestoes_musicas');
    }
};
