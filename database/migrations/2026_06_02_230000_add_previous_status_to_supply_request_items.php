<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supply_request_items', function (Blueprint $table) {
            $table->enum('previous_status', ['pending','quoting','awaitingPayment','awaitingDelivery','received','cancelled','cancelRequested'])
                  ->nullable()
                  ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('supply_request_items', function (Blueprint $table) {
            $table->dropColumn('previous_status');
        });
    }
};
