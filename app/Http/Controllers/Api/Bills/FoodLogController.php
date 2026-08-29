<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodLogController extends BillsApiController
{
    public function destroyGeneralItem(Request $request): JsonResponse
    {
        return $this->legacy('removeFoodLogGeneralItem.php', $request);
    }
    public function destroyItem(Request $request): JsonResponse
    {
        return $this->legacy('removeFoodLogItem.php', $request);
    }
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('loadFoodLog.php', $request);
    }
    public function sensitivities(Request $request): JsonResponse
    {
        return $this->legacy('loadFoodSensitivities.php', $request);
    }
    public function sensitivitiesGeneral(Request $request): JsonResponse
    {
        return $this->legacy('loadFoodSensitivitiesGeneral.php', $request);
    }
    public function storeGeneralItem(Request $request): JsonResponse
    {
        return $this->legacy('addFoodLogGeneralItem.php', $request);
    }
    public function storeItem(Request $request): JsonResponse
    {
        return $this->legacy('addFoodLogItem.php', $request);
    }
    public function updateGeneralItem(Request $request): JsonResponse
    {
        return $this->legacy('editFoodLogGeneralItem.php', $request);
    }
}
