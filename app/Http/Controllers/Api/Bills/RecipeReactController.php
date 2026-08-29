<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeReactController extends BillsApiController
{
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('recipe_react/get_recipes.php', $request);
    }
}
