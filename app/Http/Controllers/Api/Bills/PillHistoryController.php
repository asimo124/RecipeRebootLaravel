<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PillHistoryController extends BillsApiController
{
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('pill_history/get_history.php', $request);
    }
}
