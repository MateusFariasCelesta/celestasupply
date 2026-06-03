<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_request_id')->constrained()->cascadeOnDelete();
            $table->string('order_number', 100)->nullable();
            $table->string('original_name');
            $table->string('path');
            $table->text('notes')->nullable();
            $table->foreignId('registered_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_orders');
    }
};
