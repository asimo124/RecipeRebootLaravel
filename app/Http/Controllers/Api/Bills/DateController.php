<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DateController extends BillsApiController
{
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('get_dates.php', $request);
    }
}
