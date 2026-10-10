<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('Property_id');
                $table->foreignId('comment_type_id');
                $table->longtext('Description');
                $table->timestamp('deleted_at')->nullable();
                $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Comments');
    }
};
