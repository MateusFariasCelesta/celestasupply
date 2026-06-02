<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_request_id')->constrained()->cascadeOnDelete();
            $table->enum('from_status', ['draft', 'pending', 'inProgress', 'completed', 'cancelled', 'cancelRequested'])->nullable();
            $table->enum('to_status', ['draft', 'pending', 'inProgress', 'completed', 'cancelled', 'cancelRequested']);
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_status_history');
    }
};
