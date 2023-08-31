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
            $table->unsignedBigInteger('statusable_id');
            $table->string('statusable_type');
            $table->enum('status',['pending','allow_HT','allowed','rejected']);
            $table->string('description');
            $table->timestamps();
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
