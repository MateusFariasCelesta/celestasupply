<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->nullable();
            $table->string('title');
            $table->string('cost_center_id', 20);
            $table->foreign('cost_center_id')->references('id')->on('cost_centers');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('urgency', ['low', 'medium', 'high'])->default('low');
            $table->enum('status', ['draft', 'pending', 'inProgress', 'completed', 'cancelled', 'cancelRequested'])->default('draft');
            $table->enum('previous_status', ['draft', 'pending', 'inProgress', 'completed', 'cancelled', 'cancelRequested'])->nullable();
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_requests');
    }
};
