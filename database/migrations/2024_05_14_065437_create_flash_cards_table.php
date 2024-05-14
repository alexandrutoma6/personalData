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
        Schema::create('flash_cards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('question');
            $table->text('answer');
            $table->string('category')->nullable();
            $table->enum('status', ['learned', 'unlearned'])->default('unlearned');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_cards');
    }
};
