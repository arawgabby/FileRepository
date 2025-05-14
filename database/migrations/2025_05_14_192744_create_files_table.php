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
            $table->string('published_by', 255)->default('null');
            $table->string('year_published', 255)->default('null');
            $table->unsignedInteger('uploaded_by');
            $table->enum('category', ['capstone', 'thesis', 'faculty_request', 'accreditation', 'admin_docs']);
            $table->string('description', 255)->default('null');
            $table->string('status', 225)->default('pending');

            $table->string('level', 255)->nullable();
            $table->string('area', 255)->nullable();
            $table->string('parameter', 255)->nullable();
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
