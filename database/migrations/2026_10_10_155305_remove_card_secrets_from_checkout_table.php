<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The checkout table stored the full card number (in an integer column) and the CVV.
 * Neither may be stored. Keep only the last four digits for display.
 * Existing card data is destroyed by this migration on purpose.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkout', function (Blueprint $table) {
            $table->string('card_last4', 4)->nullable()->after('price');
        });

        Schema::table('checkout', function (Blueprint $table) {
            $table->dropColumn(['card_number', 'cvv', 'expDate']);
        });
    }

    public function down(): void
    {
        // The secrets are gone for good; restore only the columns, not the data.
        Schema::table('checkout', function (Blueprint $table) {
            $table->integer('card_number')->nullable();
            $table->integer('cvv')->nullable();
            $table->date('expDate')->nullable();
        });

        Schema::table('checkout', function (Blueprint $table) {
            $table->dropColumn('card_last4');
        });
    }
};
