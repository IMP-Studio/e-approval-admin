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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            // $table->enum('level_permission',[0,1,2,3,4]);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->mediumText('facePoint')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->datetime('deleted_at')->nullable();
            // 0 untuk employee
            // 1 untuk HT
            // 2 untuk HR
            // 3 untuk CTO || CEO || COO
            // 4 untuk SuperAdmin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }

};
