<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillsAdminController extends BillsApiController
{
    public function destroy(Request $request): JsonResponse
    {
        return $this->legacy('bills_admin/delete.php', $request);
    }
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('bills_admin/list.php', $request);
    }
    public function show(Request $request): JsonResponse
    {
        return $this->legacy('bills_admin/get.php', $request);
    }
    public function store(Request $request): JsonResponse
    {
        return $this->legacy('bills_admin/create.php', $request);
    }
    public function update(Request $request): JsonResponse
    {
        return $this->legacy('bills_admin/update.php', $request);
    }
    public function updateAudit(Request $request): JsonResponse
    {
        return $this->legacy('bills_admin/update_audit.php', $request);
    }
    public function updateFlags(Request $request): JsonResponse
    {
        return $this->legacy('bills_admin/update_flags.php', $request);
    }
}
