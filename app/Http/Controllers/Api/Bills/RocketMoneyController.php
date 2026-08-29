<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RocketMoneyController extends BillsApiController
{
    public function show(Request $request): JsonResponse
    {
        return $this->legacy('loadRocketMoneyData.php', $request);
    }
    public function updateCollapsed(Request $request): JsonResponse
    {
        return $this->legacy('updateRocketMoneyCollapsed.php', $request);
    }
}
