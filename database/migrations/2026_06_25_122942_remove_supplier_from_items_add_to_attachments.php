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
        Schema::table('supply_request_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });

        Schema::table('request_attachments', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('order_number')->constrained('suppliers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_attachments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
        });

        Schema::table('supply_request_items', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('notes')->constrained('suppliers');
        });
    }
};
