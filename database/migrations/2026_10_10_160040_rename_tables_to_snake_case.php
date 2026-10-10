<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
