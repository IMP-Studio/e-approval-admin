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
        Schema::create('status_commits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->foreign('approver_id')->references('id')->on('users');
            $table->unsignedBigInteger('statusable_id');
            $table->string('statusable_type');
            $table->enum('status',['pending','preliminary','allowed','rejected']);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['statusable_id', 'statusable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_commits');
    }
};
