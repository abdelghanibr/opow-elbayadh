<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->unsignedBigInteger('complex_id')->nullable()->after('user_id');
            $table->index('complex_id', 'persons_complex_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropIndex('persons_complex_id_index');
            $table->dropColumn('complex_id');
        });
    }
};
