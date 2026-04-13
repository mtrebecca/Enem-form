<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('provas', function (Blueprint $table): void {
            $table->string('tipo', 24)->default('simulado')->after('status');
        });

        Schema::table('questoes', function (Blueprint $table): void {
            $table->text('fonte')->nullable()->after('opcoes');
        });
    }

    public function down(): void
    {
        Schema::table('questoes', function (Blueprint $table): void {
            $table->dropColumn('fonte');
        });

        Schema::table('provas', function (Blueprint $table): void {
            $table->dropColumn('tipo');
        });
    }
};
