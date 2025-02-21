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
        Schema::create('property_features', function (Blueprint $table) {
            $table->unsignedBigInteger('Property_id')->primary();
            $table->integer('Bedrooms')->default(1);
            $table->integer('Bathrooms')->default(1);
            $table->integer('Kitchen')->default(1);
            $table->integer('Rooms')->default(1);
            $table->integer('Area')->default(30);
            $table->boolean('Heating')->default(1);
            $table->boolean('Air_Conditioner')->default(1);
            $table->boolean('Parking')->default(1);
            $table->timestamps('updated_at');
            $table->timestamp('deleted_at')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_features');
    }
};
