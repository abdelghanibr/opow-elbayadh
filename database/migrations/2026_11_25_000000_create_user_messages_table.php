<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_messages')) {
            return;
        }

        Schema::create('user_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->string('code', 120)->nullable();      // clé d'idempotence d'un envoi (ex. recalcul-20261125-2095)
            $table->string('title');
            $table->text('body');
            $table->string('sent_by', 120)->nullable();   // « الإدارة »
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->unique(['user_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_messages');
    }
};
