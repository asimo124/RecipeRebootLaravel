<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpensesAppController extends BillsApiController
{
    public function show(Request $request): JsonResponse
    {
        return $this->legacy('loadExpensesAppData.php', $request);
    }
    public function updateCollapsed(Request $request): JsonResponse
    {
        return $this->legacy('updateExpensesAppCollapsed.php', $request);
    }
}
