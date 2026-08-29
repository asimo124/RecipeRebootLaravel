<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaycheckController extends BillsApiController
{
    public function updateDisposable(Request $request): JsonResponse
    {
        return $this->legacy('updatePaycheckDisposable.php', $request);
    }
}
