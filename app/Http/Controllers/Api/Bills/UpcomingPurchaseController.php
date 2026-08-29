<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpcomingPurchaseController extends BillsApiController
{
    public function destroy(Request $request): JsonResponse
    {
        return $this->legacy('removeUpcomingPurchase.php', $request);
    }
    public function store(Request $request): JsonResponse
    {
        return $this->legacy('addUpcomingPurchase.php', $request);
    }
}
