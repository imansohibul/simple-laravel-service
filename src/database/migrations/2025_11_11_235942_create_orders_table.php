<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // INT Primary Key, Auto Increment
            $table->unsignedBigInteger('user_id'); // FK to users.id
            $table->timestamp('created_at')->useCurrent(); // DATETIME Default: Current Timestamp

            // Define Foreign Key
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
