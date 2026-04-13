<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('provas', 'tipo')) {
            return;
        }

        DB::table('provas')->whereNull('tipo')->update(['tipo' => 'simulado']);
    }

    public function down(): void
    {
    }
};
