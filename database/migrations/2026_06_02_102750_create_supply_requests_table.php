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
        Schema::create('supply_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->nullable();
            $table->string('title');
            $table->string('cost_center_id', 20);
            $table->foreign('cost_center_id')->references('id')->on('cost_centers');
            $table->foreignId('user_id')->constrained('users');
            $table->string('urgency')->default('low');
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_requests');
    }
};
