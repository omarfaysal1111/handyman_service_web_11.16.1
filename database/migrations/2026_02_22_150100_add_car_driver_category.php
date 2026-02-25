<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $existing = DB::table('categories')
            ->where('name', 'Car / Driver')
            ->orWhere('slug', 'car-driver')
            ->first();

        if (!$existing) {
            DB::table('categories')->insert([
                'name' => 'Car / Driver',
                'description' => 'Book reliable car and driver services for pickups, drops, and city rides.',
                'color' => '#000000',
                'status' => 1,
                'is_featured' => 1,
                'slug' => Str::slug('Car / Driver'),
                'seo_enabled' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')
            ->where('name', 'Car / Driver')
            ->orWhere('slug', 'car-driver')
            ->delete();
    }
};
