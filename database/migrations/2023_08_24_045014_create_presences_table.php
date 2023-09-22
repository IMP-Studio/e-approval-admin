<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('category',['telework','WFO','work_trip','leave','skip']);
            $table->time('entry_time');
            $table->time('temporary_entry_time')->nullable();
            $table->time('exit_time')->default('00:00:00');
            $table->date('date');
            $table->decimal('latitude')->nullable();
            $table->decimal('longitude')->nullable();
            $table->text('emergency_description')->nullable();
            $table->timestamps();
            $table->datetime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');

    }
};
