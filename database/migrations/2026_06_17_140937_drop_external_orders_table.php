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
        Schema::dropIfExists('external_orders');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('external_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_request_id')->constrained('supply_requests')->onDelete('cascade');
            $table->string('order_number')->nullable();
            $table->string('original_name');
            $table->string('path');
            $table->text('notes')->nullable();
            $table->foreignId('registered_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }
};
