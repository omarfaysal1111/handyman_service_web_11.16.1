<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert Provider Types
        DB::table('provider_types')->insert([
            ['name' => 'Tourist Office', 'commission' => 0, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tourism Company', 'commission' => 0, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert Payment Gateways
        DB::table('payment_gateways')->insert([
            [
                'title' => 'InstaPay',
                'type' => 'instapay',
                'status' => 1,
                'is_test' => 1,
                'value' => null,
                'live_value' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Vodafone Cash',
                'type' => 'vodafone_cash',
                'status' => 1,
                'is_test' => 1,
                'value' => null,
                'live_value' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'PayPal',
                'type' => 'paypal',
                'status' => 1,
                'is_test' => 1,
                'value' => json_encode(['paypal_client_id' => '', 'paypal_secret' => '']),
                'live_value' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('provider_types')->whereIn('name', ['Tourist Office', 'Tourism Company'])->delete();
        DB::table('payment_gateways')->whereIn('type', ['instapay', 'vodafone_cash', 'paypal'])->delete();
    }
};
