<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChargeController extends BillsApiController
{
    public function categories(Request $request): JsonResponse
    {
        return $this->legacy('get_categories.php', $request);
    }
    public function final(Request $request): JsonResponse
    {
        return $this->legacy('get_final_charges.php', $request);
    }
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('get_charges.php', $request);
    }
    public function indexByCategory(Request $request): JsonResponse
    {
        return $this->legacy('get_charges_by_category.php', $request);
    }
    public function load(Request $request): JsonResponse
    {
        return $this->legacy('load_charges.php', $request);
    }
    public function loadAlt(Request $request): JsonResponse
    {
        return $this->legacy('load_charges2.php', $request);
    }
    public function show(Request $request): JsonResponse
    {
        return $this->legacy('get_charge_detail.php', $request);
    }
    public function updateCategory(Request $request): JsonResponse
    {
        return $this->legacy('save_charge_category.php', $request);
    }
}
