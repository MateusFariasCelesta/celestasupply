<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('cost_centers', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('suppliers', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('items', function (Blueprint $t) {
            $t->dropSoftDeletes();
            $t->boolean('isActive')->default(true)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->softDeletes());
        Schema::table('cost_centers', fn (Blueprint $t) => $t->softDeletes());
        Schema::table('suppliers', fn (Blueprint $t) => $t->softDeletes());
        Schema::table('items', function (Blueprint $t) {
            $t->softDeletes();
            $t->dropColumn('isActive');
        });
    }
};
