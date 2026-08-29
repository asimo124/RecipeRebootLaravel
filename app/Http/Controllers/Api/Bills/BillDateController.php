<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillDateController extends BillsApiController
{
    public function commitEndDates(Request $request): JsonResponse
    {
        return $this->legacy('commitNewEndDates.php', $request);
    }
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('loadBillDates.php', $request);
    }
    public function indexAlt(Request $request): JsonResponse
    {
        return $this->legacy('loadBillDates2.php', $request);
    }
    public function updateEnabled(Request $request): JsonResponse
    {
        return $this->legacy('updateBillDateEnabled.php', $request);
    }
    public function updateMultiplier(Request $request): JsonResponse
    {
        return $this->legacy('updateBillDateMultiplier.php', $request);
    }
}
