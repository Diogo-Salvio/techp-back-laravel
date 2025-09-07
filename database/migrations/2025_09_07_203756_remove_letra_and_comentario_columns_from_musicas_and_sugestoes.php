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
        // Remover colunas da tabela musicas
        Schema::table('musicas', function (Blueprint $table) {
            $table->dropColumn(['letra', 'comentario_sugestao']);
        });

        // Remover colunas da tabela sugestoes_musicas
        Schema::table('sugestoes_musicas', function (Blueprint $table) {
            $table->dropColumn(['letra', 'comentario_sugestao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Adicionar colunas de volta na tabela musicas
        Schema::table('musicas', function (Blueprint $table) {
            $table->text('letra')->nullable();
            $table->text('comentario_sugestao')->nullable();
        });

        // Adicionar colunas de volta na tabela sugestoes_musicas
        Schema::table('sugestoes_musicas', function (Blueprint $table) {
            $table->text('letra')->nullable();
            $table->text('comentario_sugestao')->nullable();
        });
    }
};
