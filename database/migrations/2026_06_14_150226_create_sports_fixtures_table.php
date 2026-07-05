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
        Schema::create('sports_fixtures', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('team_a_name');
            $table->string('team_a_logo')->nullable();
            $table->string('team_a_score')->nullable();
            $table->string('team_a_abbrev')->nullable();
            $table->string('team_b_name');
            $table->string('team_b_logo')->nullable();
            $table->string('team_b_score')->nullable();
            $table->string('team_b_abbrev')->nullable();
            $table->string('match_status'); // e.g. "Full Time", "Upcoming"
            $table->dateTime('match_time')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sports_fixtures');
    }
};
