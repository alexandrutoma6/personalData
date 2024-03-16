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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('location');
            $table->string('link');
            $table->json('recurrence');
            $table->boolean('all_day');
            $table->boolean('is_recurrent');
            $table->boolean('synced_google');
            $table->string('google_calendar_id');
            $table->foreignId('owner_user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
