<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Early development databases had a stray users.property_id foreign key.
 * Fresh installs never have it, so this does nothing there (and works on any database driver).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'property_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
        });
    }

    public function down(): void
    {
        // nothing to restore: the column was never part of the schema
    }
};