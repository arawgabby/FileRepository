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
        Schema::create('folder_access', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('folder_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('assigned_by')->nullable();
            $table->text('note')->nullable();
            $table->string('status', 255)->nullable();

            $table->unique(['folder_id', 'user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folder_access');
    }
};
