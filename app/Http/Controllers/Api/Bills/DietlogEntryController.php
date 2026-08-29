<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DietlogEntryController extends BillsApiController
{
    public function destroy(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_entry_delete.php', $request);
    }
    public function store(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_entry_create.php', $request);
    }
    public function update(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_entry_update.php', $request);
    }
}
