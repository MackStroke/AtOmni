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
        Schema::create('traffic_report_hourlies', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->integer('hour'); // 0-23
            $table->integer('page_views')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->decimal('data_consumed_mb', 10, 2)->default(0);
            $table->timestamps();

            // Unique constraint on date and hour
            $table->unique(['report_date', 'hour']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_report_hourlies');
    }
};
