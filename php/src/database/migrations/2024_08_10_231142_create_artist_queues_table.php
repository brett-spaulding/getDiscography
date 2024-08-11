<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('artist_queues', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('artist_id')->constrained('artists');
            $table->foreignId('album_id')->nullable()->constrained('albums');
            $table->enum('state', [
                'pending',
                'in_progress',
                'done',
            ])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_queues');
    }
};
