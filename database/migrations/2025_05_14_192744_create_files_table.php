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
        Schema::create('files', function (Blueprint $table) {
            $table->increments('file_id');
            $table->string('filename', 255);
            $table->string('file_path', 255);
            $table->integer('file_size');
            $table->string('file_type', 50);
            $table->string('authors', 500)->nullable();
            $table->string('published_by', 255)->nullable();
            $table->string('year_published', 255)->nullable();
            $table->unsignedInteger('uploaded_by');
            $table->string('category', 255)->nullable(); // ['capstone', 'thesis', 'faculty_request', 'accreditation', 'admin_docs', 'custom_location']
            $table->string('description', 255)->nullable();
            $table->string('status', 225)->default('pending');

            $table->string('level', 255)->nullable();
            $table->string('phase', 255)->nullable();
            $table->string('area', 255)->nullable();
            $table->string('parameter', 255)->nullable();
            $table->string('character', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
