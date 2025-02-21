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
       Schema::create('Properties' , function (Blueprint $table) {
        $table->id();
        $table->foreignId('Dealer_id')->constrained('users')->onDelete('cascade');;
        $table->foreignId('Property_type_id');
        $table->foreignId('city_id');
        
        $table->integer('year');
        $table->integer('price');
        $table->integer('phone')->nullable();
        $table->string('address',255);  
        $table->longText('description')->nullable();
        $table->enum('status', ['Available', 'Not Available'])->default('Available'); 
        $table->timestamp('published_at')->nullable();
        $table->timestamps();
        $table->timestamp('deleted_at')->nullable();
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Properties');
    }
};
