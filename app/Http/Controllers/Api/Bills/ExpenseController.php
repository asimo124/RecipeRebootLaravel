<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends BillsApiController
{
    public function destroy(Request $request): JsonResponse
    {
        return $this->legacy('expenses/delete.php', $request);
    }
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('expenses/list.php', $request);
    }
    public function store(Request $request): JsonResponse
    {
        return $this->legacy('expenses/create.php', $request);
    }
    public function sync(Request $request): JsonResponse
    {
        return $this->legacy('syncExpenses.php', $request);
    }
    public function update(Request $request): JsonResponse
    {
        return $this->legacy('expenses/update.php', $request);
    }
}
