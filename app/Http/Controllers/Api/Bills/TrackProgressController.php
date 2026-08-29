<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackProgressController extends BillsApiController
{
    public function calculate(Request $request): JsonResponse
    {
        return $this->legacy('calcTrackProgress.php', $request);
    }
}
