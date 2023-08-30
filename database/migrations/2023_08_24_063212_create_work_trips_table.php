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
        Schema::create('work_trips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('presence_id');
            $table->foreign('presence_id')->references('id')->on('presences');
            $table->string('file');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('entry_date');
            $table->mediumText('face_point');
            $table->enum('status',['pending','allowed','rejected']);
            $table->string('description');
            $table->timestamps();
            $table->datetime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_trips');
    }
};
