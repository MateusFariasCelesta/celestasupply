<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['requester', 'buyer', 'admin'])->default('requester')->after('password');
            $table->string('whatsapp_phone', 20)->nullable()->after('role');
            $table->tinyInteger('isActive')->default(1)->after('whatsapp_phone');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'whatsapp_phone', 'isActive']);
            $table->dropSoftDeletes();
        });
    }
};
