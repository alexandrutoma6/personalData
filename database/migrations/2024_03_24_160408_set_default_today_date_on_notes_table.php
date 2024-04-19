<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->date('date')->default(Carbon::today())->after('favorite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
