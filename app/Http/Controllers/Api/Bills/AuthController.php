<?php

namespace App\Http\Controllers\Api\Bills;

use App\Services\Bills\BillsAuthService;
use App\Services\Bills\LegacyBillsScriptRunner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BillsApiController
{
    public function __construct(
        LegacyBillsScriptRunner $legacyRunner,
        protected BillsAuthService $auth,
    ) {
        parent::__construct($legacyRunner);
    }

    public function login(Request $request): JsonResponse
    {
        return $this->auth->login($request);
    }

    public function me(Request $request): JsonResponse
    {
        return $this->auth->me($request);
    }

    public function logout(Request $request): JsonResponse
    {
        return $this->auth->logout($request);
    }
}
