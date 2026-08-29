<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DietlogLogController extends BillsApiController
{
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_log.php', $request);
    }
    public function indexPublic(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_log_public.php', $request);
    }
}
