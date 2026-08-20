<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Resources\HomeInventoryResource;
use App\Models\HomeInventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InventoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $items = HomeInventory::query()
            ->with(['ingredient.type'])
            ->orderBy('id')
            ->get();

        return HomeInventoryResource::collection($items);
    }

    public function store(StoreInventoryRequest $request): HomeInventoryResource|JsonResponse
    {
        $ingredientId = $request->validated()['ingredient_id'];

        $existing = HomeInventory::query()->where('ingredient_id', $ingredientId)->first();
        if ($existing) {
            $existing->load(['ingredient.type']);

            return new HomeInventoryResource($existing);
        }

        $item = HomeInventory::query()->create(['ingredient_id' => $ingredientId]);
        $item->load(['ingredient.type']);

        return new HomeInventoryResource($item);
    }

    public function destroy(HomeInventory $inventory): JsonResponse
    {
        $inventory->delete();

        return response()->json(null, 204);
    }
}
