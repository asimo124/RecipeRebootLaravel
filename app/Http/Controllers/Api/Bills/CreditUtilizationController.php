<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditUtilizationController extends BillsApiController
{
    public function bills(Request $request): JsonResponse
    {
        return $this->legacy('credit_utilization/bills.php', $request);
    }
    public function destroy(Request $request): JsonResponse
    {
        return $this->legacy('credit_utilization/delete.php', $request);
    }
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('credit_utilization/list.php', $request);
    }
    public function show(Request $request): JsonResponse
    {
        return $this->legacy('credit_utilization/get.php', $request);
    }
    public function store(Request $request): JsonResponse
    {
        return $this->legacy('credit_utilization/create.php', $request);
    }
    public function update(Request $request): JsonResponse
    {
        return $this->legacy('credit_utilization/update.php', $request);
    }
}
