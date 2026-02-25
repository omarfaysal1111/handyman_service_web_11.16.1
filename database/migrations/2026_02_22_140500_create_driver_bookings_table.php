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
        Schema::create('driver_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->unique();
            $table->unsignedBigInteger('driver_id');
            $table->string('status')->default('requested');
            $table->double('driver_latitude', 10, 7)->nullable();
            $table->double('driver_longitude', 10, 7)->nullable();
            $table->double('pickup_latitude', 10, 7)->nullable();
            $table->double('pickup_longitude', 10, 7)->nullable();
            $table->double('drop_latitude', 10, 7)->nullable();
            $table->double('drop_longitude', 10, 7)->nullable();
            $table->text('pickup_address')->nullable();
            $table->text('drop_address')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_bookings');
    }
};
