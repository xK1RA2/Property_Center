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
        $table->foreignId('Property_type_id')->constrained('Property_type');
        $table->foreignId('city_id')->constrained('cities');
        $table->string('PurchaseType');
        $table->integer('year');
        $table->integer('price');
        $table->integer('phone')->nullable();
        $table->string('address',255);  
        $table->longText('description')->nullable();
        $table->enum('status', ['Available', 'Not Available'])->default('Available'); 
        $table->timestamp('published_at')->nullable();
        $table->timestamp('deleted_at')->nullable();
        $table->timestamps();
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
