<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_achievers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('tier_number');
            $table->unsignedInteger('referral_count_at_achievement');
            $table->decimal('reward_amount', 10, 2);
            $table->date('achieved_at');
            $table->timestamps();

            $table->index(['member_id', 'promotion_id', 'tier_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_achievers');
    }
};
