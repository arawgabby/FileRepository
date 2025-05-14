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
        Schema::create('file_time_stamps', function (Blueprint $table) {
            $table->increments('timestamp_id');
            $table->unsignedInteger('file_id');
            $table->unsignedInteger('version_id')->nullable();
            $table->string('event_type', 255)->default('NULL');
            $table->dateTime('timestamp');
            $table->timestamp('recorded_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_time_stamps');
    }
};
