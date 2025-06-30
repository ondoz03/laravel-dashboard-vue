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
        Schema::create('master_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('category_item_id')->nullable();
            $table->string('aol_id')->nullable();
            $table->string('item_code')->unique();
            $table->string('item_name');
            $table->string('item_category')->nullable();
            $table->string('buyer')->nullable();
            $table->decimal('ppn', 10, 2)->default(0);
            $table->decimal('pph', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_items');
    }
};
