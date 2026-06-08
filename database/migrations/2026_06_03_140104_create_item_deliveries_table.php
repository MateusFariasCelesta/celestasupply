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
        Schema::create('item_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_request_item_id')->constrained('supply_request_items')->cascadeOnDelete();
            $table->decimal('quantity', 10, 3);
            $table->string('notes', 500)->nullable();
            $table->foreignId('registered_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_deliveries');
    }
};
