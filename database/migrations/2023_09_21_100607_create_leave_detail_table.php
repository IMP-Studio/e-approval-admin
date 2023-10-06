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
        Schema::create('leave_detail', function (Blueprint $table) {
            $table->id();
            $table->text('description_leave');
            $table->unsignedBigInteger('type_of_leave_id')->nullable();
            $table->foreign('type_of_leave_id')->references('id')->on('type_of_leave');
            $table->integer('days');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_detail');
    }
};
