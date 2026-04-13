<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->string('type', 50); // profile_image, proof_of_address
            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->timestamps();

            $table->index(['documentable_type', 'documentable_id', 'type'], 'doc_morph_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
