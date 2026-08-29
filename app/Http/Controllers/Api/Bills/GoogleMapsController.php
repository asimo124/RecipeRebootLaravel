<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoogleMapsController extends BillsApiController
{
    public function tripDuration(Request $request): JsonResponse
    {
        return $this->legacy('request_google_maps_trip_duration.php', $request);
    }
}
