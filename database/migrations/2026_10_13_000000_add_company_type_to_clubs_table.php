<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('clubs', 'company_type')) {
            Schema::table('clubs', function (Blueprint $table) {
                $table->string('company_type', 20)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clubs', 'company_type')) {
            Schema::table('clubs', function (Blueprint $table) {
                $table->dropColumn('company_type');
            });
        }
    }
};
