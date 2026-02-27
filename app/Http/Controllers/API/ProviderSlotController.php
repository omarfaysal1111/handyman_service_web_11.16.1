<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProviderSlotMapping;
use Illuminate\Support\Facades\Auth;

class ProviderSlotController extends Controller
{
    public function getProviderSlot(Request $request)
    {
        $provider_id = $request->provider_id ?? auth()->user()->id;
        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        $slotsArray = [];
        foreach ($days as $value) {
            $slot = ProviderSlotMapping::where('provider_id', $provider_id)
                ->where('days', $value)
                ->orderBy('start_at', 'asc')
                ->pluck('start_at')
                ->toArray();

            $obj = [
                "day" => $value,
                "slot" => $slot,
            ];
            array_push($slotsArray, $obj);
        }

        return comman_custom_response($slotsArray);
    }

    public function providerAllServicesTimeslots(Request $request)
    {
        return $this->getProviderSlot($request);
    }

    public function getServiceSlot(Request $request)
    {
        return $this->getProviderSlot($request);
    }

    public function saveServiceSlot(Request $request)
    {
        $slotdata = $request->all();
        $provider_id = $request->provider_id ?? Auth::id();
        $message = __('messages.updated');

        $provider_slot = ProviderSlotMapping::where('provider_id', $provider_id)->get();
        if (count($provider_slot) > 0) {
            $provider_slot->each->delete();
        }

        if (!empty($slotdata['slots'])) {
            foreach ($slotdata['slots'] as $value) {
                if (!empty($value['time'])) {
                    foreach ($value['time'] as $time) {
                        $slotArray = [
                            'provider_id' => $provider_id,
                            'days' => $value['day'],
                            'start_at' => $time,
                            'end_at' => date('H:i', strtotime('+1 hour', strtotime($time))),
                        ];
                        $res = ProviderSlotMapping::create($slotArray);
                        $message = __('messages.update_form', ['form' => __('messages.providerslot')]);
                        if ($res->wasRecentlyCreated) {
                            $message = __('messages.save_form', ['form' => __('messages.providerslot')]);
                        }
                    }
                }
            }
        }

        return comman_message_response($message);
    }
}
