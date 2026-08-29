<?php

namespace App\Http\Controllers\Api\Bills;

use App\Http\Controllers\Controller;
use App\Services\Bills\LegacyBillsScriptRunner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BillsApiController extends Controller
{
    public function __construct(protected LegacyBillsScriptRunner $legacyRunner)
    {
    }

    protected function legacy(string $script, Request $request): JsonResponse
    {
        return $this->legacyRunner->run($script, $request);
    }
}
