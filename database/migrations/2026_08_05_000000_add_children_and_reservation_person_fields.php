<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('user_id');
            $table->json('guardian_docs')->nullable()->after('parent_id');

            $table->index('parent_id', 'persons_parent_id_index');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('person_id')->nullable()->after('user_id');

            $table->index('person_id', 'reservations_person_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropIndex('persons_parent_id_index');
            $table->dropColumn(['parent_id', 'guardian_docs']);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('reservations_person_id_index');
            $table->dropColumn('person_id');
        });
    }
};
