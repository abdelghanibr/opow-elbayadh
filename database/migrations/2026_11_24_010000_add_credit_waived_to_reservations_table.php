<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Petit reste abandonné quand la réservation est réglée par remboursement
            $table->decimal('credit_waived', 10, 2)->default(0)->after('credit_discount');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('credit_waived');
        });
    }
};
