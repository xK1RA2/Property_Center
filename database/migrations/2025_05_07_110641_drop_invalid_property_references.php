<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

  public function up()
{
    Schema::disableForeignKeyConstraints();
    
    // Try dropping any lingering constraints (SQLite syntax)
    DB::statement('PRAGMA foreign_keys=OFF');
    
    // If you know which table has the bad reference, drop its foreign keys
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['property_id']); // Adjust column name if needed
    });
    
    DB::statement('PRAGMA foreign_keys=ON');
    Schema::enableForeignKeyConstraints();
}
};
