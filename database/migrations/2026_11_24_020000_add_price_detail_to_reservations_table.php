<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Détail figé à la création : séances (total/comptées/prix de séance) et soldes de remboursement avant/après
            $table->json('price_detail')->nullable()->after('credit_note');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('price_detail');
        });
    }
};
