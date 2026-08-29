<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DietlogController extends BillsApiController
{
    public function addOatmeal(Request $request): JsonResponse
    {
        return $this->legacy('dietlog/add_oatmeal.php', $request);
    }
    public function inc(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_inc.php', $request);
    }
    public function lookups(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_lookups.php', $request);
    }
    public function suggestedMeal(Request $request): JsonResponse
    {
        return $this->legacy('dietlog_suggested_meal.php', $request);
    }
}
