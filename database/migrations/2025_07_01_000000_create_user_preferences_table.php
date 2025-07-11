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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('preference_type'); // 'columns', 'theme', etc.
            $table->string('page'); // 'master_items', 'users', etc.
            $table->json('settings'); // Store column visibility, theme color, etc.
            $table->timestamps();

            // Unique constraint to ensure one preference type per page per user
            $table->unique(['user_id', 'preference_type', 'page']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
