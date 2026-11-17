<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itmosoft_seller_verifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('seller');
            $table->string('reference')->unique();
            $table->string('status', 32)->default('draft')->index();
            $table->char('country_code', 2)->nullable();
            $table->string('business_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->nullableMorphs('reviewer');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['seller_type', 'seller_id', 'status'], 'itmosoft_sv_seller_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itmosoft_seller_verifications');
    }
};
