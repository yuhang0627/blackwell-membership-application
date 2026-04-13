<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('tier_number');
            $table->unsignedInteger('referral_count');       // milestone: 10, 50, 100
            $table->decimal('reward_amount', 10, 2);         // USD
            $table->enum('type', ['fixed', 'recurring'])->default('fixed');
            $table->unsignedInteger('recurring_interval')->nullable(); // e.g. 10 for "every 10"
            $table->timestamps();

            $table->unique(['promotion_id', 'tier_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_tiers');
    }
};
