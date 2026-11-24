<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name')->nullable();
            $table->string('user_type', 40)->nullable();
            // created / updated / deleted / login / logout / login_failed / request / sql_delete / backup / backup_failed
            $table->string('event', 40)->index();
            $table->string('auditable_type')->nullable()->index();
            $table->string('auditable_id', 64)->nullable();
            $table->longText('old_values')->nullable();
            $table->longText('new_values')->nullable();
            $table->string('route', 190)->nullable();
            $table->string('method', 10)->nullable();
            $table->text('url')->nullable();
            $table->string('ip', 64)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
