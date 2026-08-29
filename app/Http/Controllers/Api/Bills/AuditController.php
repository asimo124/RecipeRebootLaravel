<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditController extends BillsApiController
{
    public function upload(Request $request): JsonResponse
    {
        return $this->legacy('audit/upload.php', $request);
    }
}
