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
        Schema::create('checkout', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id'); 
            $table->foreignId('dealer_id'); 
            $table->foreignId('property_id'); 
            $table->integer('price'); 
            $table->integer('card_number');
            $table->date('expDate'); 
            $table->date('from')->nullable(); 
            $table->date('to')->nullable(); 
            $table->integer('cvv'); 
            $table->string('address'); 
            $table->timestamp('date');
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('checkout');
    }
};
