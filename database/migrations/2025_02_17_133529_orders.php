<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('Property_id')->constrained('properties')->onDelete('cascade');
            $table->enum('status', ['Approved','Pending','Canceled'])->default('Approved');
            $table->integer('price'); 
            $table->timestamp('date');
        });
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
