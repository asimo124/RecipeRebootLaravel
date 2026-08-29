<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends BillsApiController
{
    public function categories(Request $request): JsonResponse
    {
        return $this->legacy('loadTransactionCategories.php', $request);
    }
    public function drilldown(Request $request): JsonResponse
    {
        return $this->legacy('loadTransactionDrilldown.php', $request);
    }
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('loadTransactionAll.php', $request);
    }
}
