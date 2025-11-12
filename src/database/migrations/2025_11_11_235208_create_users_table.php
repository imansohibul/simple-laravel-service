<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // INT, Primary Key, Auto Increment
            $table->string('email', 255)->unique(); // VARCHAR(255) Unique, Not Null
            $table->string('password', 255); // VARCHAR(255) Not Null
            $table->string('name', 255); // VARCHAR(255) Not Null
            $table->enum('role', ['user', 'manager', 'administrator'])->default('user'); // ENUM Default: user
            $table->boolean('active')->default(true); // BOOLEAN Default: true
            $table->timestamp('created_at')->useCurrent(); // DATETIME Default: Current Timestamp
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
