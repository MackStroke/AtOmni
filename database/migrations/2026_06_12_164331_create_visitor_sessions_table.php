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
        Schema::create('visitor_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referrer')->nullable();
            $table->string('channel')->default('Direct'); // Direct, Organic Search, Social, Referral
            $table->string('city')->nullable();
            $table->boolean('is_new_visitor')->default(true);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('last_activity_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('page_views')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_sessions');
    }
};
