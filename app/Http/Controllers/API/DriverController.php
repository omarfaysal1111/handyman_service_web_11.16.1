<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DriverBooking;
use App\Models\ProviderAddressMapping;
use App\Models\User;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function findDrivers(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:0.1',
        ]);

        $latitude = (float) $request->latitude;
        $longitude = (float) $request->longitude;
        $radius = (float) ($request->radius ?? 10);
        $unit = getSettingKeyValue('site-setup', 'distance_type') ?? 'km';

        $drivers = User::where('user_type', 'handyman')
            ->where('status', 1)
            ->where('is_available', 1)
            ->withCount('handymanBooking as total_rides')
            ->withAvg('handymanRating as rating_avg', 'rating')
            ->get();

        $driverList = [];

        foreach ($drivers as $driver) {
            $address = null;

            if (!empty($driver->service_address_id)) {
                $address = ProviderAddressMapping::find($driver->service_address_id);
            }

            if (!$address && !empty($driver->provider_id)) {
                $address = ProviderAddressMapping::where('provider_id', $driver->provider_id)
                    ->where('status', 1)
                    ->latest('id')
                    ->first();
            }

            if (!$address || $address->latitude === null || $address->longitude === null) {
                continue;
            }

            $driverLatitude = (float) $address->latitude;
            $driverLongitude = (float) $address->longitude;
            $distance = $this->calculateDistance($latitude, $longitude, $driverLatitude, $driverLongitude, $unit);

            if ($distance > $radius) {
                continue;
            }

            $driverList[] = [
                'id' => $driver->id,
                'name' => trim(($driver->first_name ?? '') . ' ' . ($driver->last_name ?? '')),
                'email' => $driver->email,
                'contact_number' => $driver->contact_number,
                'profile_image' => $driver->login_type !== null && $driver->login_type !== 'mobile'
                    ? $driver->social_image
                    : getSingleMedia($driver, 'profile_image', null),
                'rating' => (float) ($driver->rating_avg ?? 0),
                'total_rides' => (int) ($driver->total_rides ?? 0),
                'latitude' => $driverLatitude,
                'longitude' => $driverLongitude,
                'distance' => round($distance, 2),
                'vehicle_name' => '',
                'vehicle_number' => '',
                'vehicle_type' => 'Car',
                'vehicle_image' => '',
                'is_available' => (bool) $driver->is_available,
                'estimated_time' => (int) max(1, round(($distance / 30) * 60)),
            ];
        }

        usort($driverList, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return comman_custom_response([
            'success' => true,
            'message' => 'Drivers fetched successfully.',
            'data' => array_values($driverList),
        ]);
    }

    public function requestDriver(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|integer|exists:bookings,id',
            'driver_id' => 'required|integer|exists:users,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'pickup_latitude' => 'nullable|numeric',
            'pickup_longitude' => 'nullable|numeric',
            'drop_latitude' => 'nullable|numeric',
            'drop_longitude' => 'nullable|numeric',
            'pickup_address' => 'nullable|string',
            'drop_address' => 'nullable|string',
        ]);

        $booking = Booking::find($request->booking_id);
        if (!$booking) {
            return comman_custom_response([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        $driver = User::where('id', $request->driver_id)
            ->where('user_type', 'handyman')
            ->first();

        if (!$driver) {
            return comman_custom_response([
                'success' => false,
                'message' => 'Selected driver is invalid.',
            ], 422);
        }

        $pickupLatitude = $request->pickup_latitude ?? $request->latitude;
        $pickupLongitude = $request->pickup_longitude ?? $request->longitude;

        $driverBooking = DriverBooking::updateOrCreate(
            ['booking_id' => $request->booking_id],
            [
                'driver_id' => $request->driver_id,
                'status' => 'requested',
                'pickup_latitude' => $pickupLatitude,
                'pickup_longitude' => $pickupLongitude,
                'drop_latitude' => $request->drop_latitude,
                'drop_longitude' => $request->drop_longitude,
                'pickup_address' => $request->pickup_address,
                'drop_address' => $request->drop_address,
            ]
        );

        $driver->is_available = 0;
        $driver->save();

        return comman_custom_response([
            'success' => true,
            'message' => 'Driver requested successfully.',
            'data' => $driverBooking,
        ]);
    }

    public function driverBookingStatus(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|integer',
        ]);

        $driverBooking = DriverBooking::with('driver')->where('booking_id', $request->booking_id)->first();

        if (!$driverBooking) {
            return comman_custom_response([
                'success' => false,
                'message' => 'Driver booking not found.',
                'data' => null,
            ], 404);
        }

        return comman_custom_response([
            'success' => true,
            'message' => 'Driver booking status fetched successfully.',
            'data' => $driverBooking,
        ]);
    }

    public function cancelDriverRequest(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|integer',
        ]);

        $driverBooking = DriverBooking::where('booking_id', $request->booking_id)->first();

        if (!$driverBooking) {
            return comman_custom_response([
                'success' => false,
                'message' => 'Driver request not found.',
            ], 404);
        }

        $driverBooking->status = 'cancelled';
        $driverBooking->save();

        $driver = User::find($driverBooking->driver_id);
        if ($driver) {
            $driver->is_available = 1;
            $driver->save();
        }

        return comman_custom_response([
            'success' => true,
            'message' => 'Driver request cancelled successfully.',
            'data' => $driverBooking,
        ]);
    }

    public function updateDriverLocation(Request $request)
    {
        $request->validate([
            'driver_booking_id' => 'required|integer|exists:driver_bookings,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $driverBooking = DriverBooking::find($request->driver_booking_id);
        $driverBooking->driver_latitude = $request->latitude;
        $driverBooking->driver_longitude = $request->longitude;
        $driverBooking->save();

        return comman_custom_response([
            'success' => true,
            'message' => 'Driver location updated successfully.',
            'data' => $driverBooking,
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = 'km')
    {
        $earthRadius = $unit === 'km' ? 6371 : 3959;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
