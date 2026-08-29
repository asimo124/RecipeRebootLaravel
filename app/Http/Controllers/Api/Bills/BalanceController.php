<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends BillsApiController
{
    public function show(Request $request): JsonResponse
    {
        return $this->legacy('getCurBalance.php', $request);
    }
    public function update(Request $request): JsonResponse
    {
        return $this->legacy('saveCurBalance.php', $request);
    }
}
