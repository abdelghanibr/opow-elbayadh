<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_id')
                  ->nullable()
                  ->after('user_id')
                  ->comment('النشاط الرياضي الوحيد للنادي');
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn('activity_id');
        });
    }
};
