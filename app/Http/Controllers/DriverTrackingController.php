<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\DriverBooking;
use Illuminate\Http\Request;

class DriverTrackingController extends Controller
{
    public function show(Request $request, int $bookingId)
    {
        $booking = Booking::find($bookingId);

        if (!$booking) {
            abort(404, 'Booking not found.');
        }

        return view('driver.tracking', compact('bookingId'));
    }

    public function status(Request $request, int $bookingId)
    {
        $driverBooking = DriverBooking::with('driver')->where('booking_id', $bookingId)->first();

        if (!$driverBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Driver request not found for this booking.',
                'data' => null,
            ], 404);
        }

        $driver = $driverBooking->driver;
        $driverName = trim(($driver->first_name ?? '') . ' ' . ($driver->last_name ?? ''));

        return response()->json([
            'success' => true,
            'message' => 'Tracking data fetched successfully.',
            'data' => [
                'id' => $driverBooking->id,
                'booking_id' => $driverBooking->booking_id,
                'driver_id' => $driverBooking->driver_id,
                'status' => $driverBooking->status,
                'driver_latitude' => $driverBooking->driver_latitude,
                'driver_longitude' => $driverBooking->driver_longitude,
                'pickup_latitude' => $driverBooking->pickup_latitude,
                'pickup_longitude' => $driverBooking->pickup_longitude,
                'drop_latitude' => $driverBooking->drop_latitude,
                'drop_longitude' => $driverBooking->drop_longitude,
                'pickup_address' => $driverBooking->pickup_address,
                'drop_address' => $driverBooking->drop_address,
                'driver_name' => $driverName !== '' ? $driverName : 'Driver',
                'driver_contact' => $driver->contact_number ?? null,
                'driver_profile_image' => $driver && $driver->login_type !== null && $driver->login_type !== 'mobile'
                    ? $driver->social_image
                    : getSingleMedia($driver, 'profile_image', null),
                'updated_at' => optional($driverBooking->updated_at)->toDateTimeString(),
            ],
        ]);
    }
}
