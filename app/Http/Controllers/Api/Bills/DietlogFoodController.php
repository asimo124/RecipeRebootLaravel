<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DietlogFoodController extends BillsApiController
{
    public function destroy(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_food_delete.php', $request);
    }
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_foods.php', $request);
    }
    public function indexPublic(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_foods_public.php', $request);
    }
    public function store(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_food_create.php', $request);
    }
    public function update(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_food_update.php', $request);
    }
}
