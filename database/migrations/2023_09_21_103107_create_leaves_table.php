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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('presence_id')->nullable();
            $table->foreign('presence_id')->references('id')->on('presences');
            $table->unsignedBigInteger('leave_detail_id');
            $table->foreign('leave_detail_id')->references('id')->on('leave_detail');
            $table->date('submission_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('file')->nullable();
            $table->string('total_leave_days');
            $table->date('entry_date');
            $table->timestamps();
            $table->datetime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
