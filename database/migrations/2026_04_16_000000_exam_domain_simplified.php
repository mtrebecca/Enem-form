<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('resultado_por_disciplina');
        Schema::dropIfExists('resultados');
        Schema::dropIfExists('user_respostas');
        Schema::dropIfExists('sessoes_prova');
        Schema::dropIfExists('opcoes_resposta');
        Schema::dropIfExists('questoes');
        Schema::dropIfExists('disciplinas');
        Schema::dropIfExists('provas');

        Schema::enableForeignKeyConstraints();

        Schema::create('provas', function (Blueprint $table): void {
            $table->id();
            $table->string('titulo');
            $table->string('status', 32)->default('ativo');
            $table->timestamps();
        });

        Schema::create('questoes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('prova_id')->constrained('provas')->cascadeOnDelete();
            $table->string('disciplina');
            $table->text('enunciado');
            $table->json('opcoes');
            $table->timestamps();
        });

        Schema::create('sessoes_prova', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('prova_id')->constrained('provas')->cascadeOnDelete();
            $table->string('status', 32)->default('em_andamento');
            $table->timestamp('iniciada_em')->useCurrent();
            $table->timestamp('finalizada_em')->nullable();
            $table->json('respostas')->nullable();
        });

        Schema::create('resultados', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sessao_id')->unique()->constrained('sessoes_prova')->cascadeOnDelete();
            $table->unsignedInteger('total_questoes');
            $table->unsignedInteger('total_acertos');
            $table->decimal('percentual_acerto', 5, 2);
            $table->json('detalhe_disciplinas');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultados');
        Schema::dropIfExists('sessoes_prova');
        Schema::dropIfExists('questoes');
        Schema::dropIfExists('provas');
    }
};
