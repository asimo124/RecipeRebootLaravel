<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DateJobController extends BillsApiController
{
    public function queue(Request $request): JsonResponse
    {
        return $this->legacy('queue_date_job.php', $request);
    }
    public function status(Request $request): JsonResponse
    {
        return $this->legacy('check_date_job_done.php', $request);
    }
}
