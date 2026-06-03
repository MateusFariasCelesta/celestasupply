<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_request_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['quote', 'invoice', 'receipt', 'other'])->default('other');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type', 100);
            $table->unsignedInteger('size_kb');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_attachments');
    }
};
