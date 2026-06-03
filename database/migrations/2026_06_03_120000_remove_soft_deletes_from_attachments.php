<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_attachments',    fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('request_attachments', fn (Blueprint $t) => $t->dropSoftDeletes());
        Schema::table('external_orders',     fn (Blueprint $t) => $t->dropSoftDeletes());
    }

    public function down(): void
    {
        Schema::table('item_attachments',    fn (Blueprint $t) => $t->softDeletes());
        Schema::table('request_attachments', fn (Blueprint $t) => $t->softDeletes());
        Schema::table('external_orders',     fn (Blueprint $t) => $t->softDeletes());
    }
};
