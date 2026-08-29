<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayPeriodController extends BillsApiController
{
    public function comingPayDates(Request $request): JsonResponse
    {
        return $this->legacy('getComingPayDates.php', $request);
    }
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('loadPayPeriods.php', $request);
    }
    public function items(Request $request): JsonResponse
    {
        return $this->legacy('loadPayPeriodItems.php', $request);
    }
    public function updateNumDays(Request $request): JsonResponse
    {
        return $this->legacy('save_pay_period_num_days.php', $request);
    }
}
