<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itmosoft_verification_documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('verification');
            $table->string('type', 80);
            $table->string('disk', 80)->default('local');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('status', 24)->default('pending')->index();
            $table->text('review_notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itmosoft_verification_documents');
    }
};
