<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->timestamp('scanned_at')->nullable()->after('qr_code');
            $table->unsignedBigInteger('scanned_by')->nullable()->after('scanned_at');
            $table->index('scanned_at');
            $table->index('scanned_by');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['scanned_at']);
            $table->dropIndex(['scanned_by']);
            $table->dropColumn(['scanned_at', 'scanned_by']);
        });
    }
};