<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends BillsApiController
{
    public function discrepancies(Request $request): JsonResponse
    {
        return $this->legacy('loadBudgetDiscrepancies.php', $request);
    }
}
