<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is destructive and can fail on existing installs due to FKs.
        // Use FK-check toggling to ensure consistent behavior across MySQL versions.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Delete all bindings to services first to avoid foreign key constraints errors
        DB::table('booking_handyman_mappings')->truncate();
        DB::table('booking_ratings')->truncate();
        DB::table('payment_histories')->truncate();
        DB::table('payments')->truncate();
        DB::table('bookings')->truncate();
        DB::table('services')->truncate();

        // Truncate categories
        DB::table('categories')->truncate();

        // Insert new categories
        $categories = [
            ['name' => 'Travel', 'slug' => Str::slug('Travel'), 'status' => 1, 'is_featured' => 1, 'color' => '#000000', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Entertainment', 'slug' => Str::slug('Entertainment'), 'status' => 1, 'is_featured' => 1, 'color' => '#000000', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ride', 'slug' => Str::slug('Ride'), 'status' => 1, 'is_featured' => 1, 'color' => '#000000', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->whereIn('name', ['Travel', 'Entertainment', 'Ride'])->delete();
    }
};
