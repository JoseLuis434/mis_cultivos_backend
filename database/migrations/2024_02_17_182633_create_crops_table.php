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
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->string('id_device')->unique();
            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->nullable();
            $table->float('days')->nullable();
            $table->string('stage')->nullable();
            $table->string('irrigation')->nullable();
            $table->float('container_area_base')->nullable();
            $table->float('container_height')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->foreignId('id_user')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crops');
    }
};
