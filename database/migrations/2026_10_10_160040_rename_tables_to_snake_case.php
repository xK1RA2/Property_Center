<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Original tables were created with mixed case and typos (Comment_Type, Rate,
 * faivorate_property, book_preview...). MySQL on Linux treats table names as
 * case-sensitive, so this also makes the schema portable.
 */
return new class extends Migration
{
    /** old => new */
    private array $tables = [
        'Comment_Type' => 'comment_types',
        'Comments' => 'comments',
        'Property_images' => 'property_images',
        'Property_type' => 'property_types',
        'Rate' => 'ratings',
        'book_preview' => 'bookings',
        'checkout' => 'checkouts',
        'faivorate_property' => 'favorites',
        'request_trader' => 'trader_requests',
        'replay' => 'replies',
    ];

    public function up(): void
    {
        foreach ($this->tables as $old => $new) {
            $this->move($old, $new);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $old => $new) {
            $this->move($new, $old);
        }
    }

    /**
     * Two steps through a temporary name: SQLite (and MySQL on Windows/macOS)
     * compare names case-insensitively, so a case-only rename can't be done directly.
     */
    private function move(string $from, string $to): void
    {
        if (!Schema::hasTable($from)) {
            return;
        }

        $tmp = $to . '__tmp';
        Schema::rename($from, $tmp);
        Schema::rename($tmp, $to);
    }
};
