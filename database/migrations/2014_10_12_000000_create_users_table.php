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
        // Create roles table first
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique(); // staff, faculty, admin
            $table->timestamps();
        });

        // Then create users table with foreign key to roles
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('email', 255);
            $table->timestamp('email_verified_at')->nullable()->default(null);
            $table->string('password', 255);
            $table->unsignedInteger('role_id');
            $table->string('contact_number', 255)->default('null');
            $table->string('remember_token', 100)->nullable()->default(null);
            $table->string('status', 255)->default('active');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);

            $table->unique('email');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
