<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('email', 100)->nullable()->after('buyer_phone');
            $table->string('identity_number', 20)->nullable()->after('email');
            $table->tinyInteger('age')->nullable()->after('identity_number');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['email', 'identity_number', 'age']);
        });
    }
};
