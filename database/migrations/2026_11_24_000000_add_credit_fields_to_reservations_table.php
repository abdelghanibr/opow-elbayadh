<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Montant déduit par le solde compensatoire (fermetures de piscine)
            $table->decimal('credit_discount', 10, 2)->default(0)->after('total_price');
            // Remarque quand la réservation est payée entièrement par remboursement
            $table->string('credit_note', 255)->nullable()->after('credit_discount');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['credit_discount', 'credit_note']);
        });
    }
};
