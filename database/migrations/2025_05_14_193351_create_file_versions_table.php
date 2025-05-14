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
        Schema::create('file_versions', function (Blueprint $table) {
            $table->increments('version_id');
            $table->unsignedInteger('file_id');
            $table->integer('version_number');
            $table->string('filename', 255);
            $table->string('file_path', 255);
            $table->integer('file_size');
            $table->string('file_type', 50);
            $table->unsignedInteger('uploaded_by');
            $table->string('status', 255)->default('active');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Foreign key constraint
            $table->foreign('file_id')->references('file_id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_versions');
    }
};
