<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BatchUpdateIngredientTypeRequest;
use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\StoreRelatedIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Http\Resources\IngredientResource;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IngredientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Ingredient::query()->with('type')->orderBy('title');

        if ($search = $request->query('search')) {
            $query->where('title', 'ilike', '%'.$search.'%');
        }

        if ($request->filled('ingredient_type_id')) {
            $query->where('ingredient_type_id', (int) $request->query('ingredient_type_id'));
        }

        if ($request->has('limit')) {
            $limit = min(max((int) $request->query('limit'), 1), 100);

            return IngredientResource::collection($query->limit($limit)->get());
        }

        $query->with('parents');

        return IngredientResource::collection($query->get());
    }

    public function batchUpdateType(BatchUpdateIngredientTypeRequest $request): AnonymousResourceCollection
    {
        $data = $request->validated();
        $ids = $data['ids'];
        $typeId = $data['ingredient_type_id'] ?? null;

        Ingredient::query()->whereIn('id', $ids)->update([
            'ingredient_type_id' => $typeId,
        ]);

        $ingredients = Ingredient::query()
            ->with(['type', 'parents'])
            ->whereIn('id', $ids)
            ->orderBy('title')
            ->get();

        return IngredientResource::collection($ingredients);
    }

    public function store(StoreIngredientRequest $request): IngredientResource
    {
        $data = $request->validated();
        $parentIds = $data['parent_ids'] ?? null;
        unset($data['parent_ids']);

        $ingredient = Ingredient::query()->create($data);
        $this->syncParents($ingredient, $parentIds);
        $ingredient->load(['type', 'parents']);

        return new IngredientResource($ingredient);
    }

    public function show(Ingredient $ingredient): IngredientResource
    {
        $ingredient->load(['type', 'parents', 'children']);

        return new IngredientResource($ingredient);
    }

    public function update(UpdateIngredientRequest $request, Ingredient $ingredient): IngredientResource
    {
        $data = $request->validated();
        $parentIds = array_key_exists('parent_ids', $data) ? $data['parent_ids'] : null;
        unset($data['parent_ids']);

        $ingredient->update($data);
        $this->syncParents($ingredient, $parentIds);
        $ingredient->load(['type', 'parents']);

        return new IngredientResource($ingredient);
    }

    public function destroy(Ingredient $ingredient): JsonResponse
    {
        $ingredient->delete();

        return response()->json(null, 204);
    }

    public function related(Ingredient $ingredient): AnonymousResourceCollection
    {
        $ingredient->load('parents');

        return IngredientResource::collection($ingredient->parents);
    }

    public function storeRelated(StoreRelatedIngredientRequest $request, Ingredient $ingredient): IngredientResource
    {
        $parentId = $request->validated()['related_ingredient_id'];
        $ingredient->parents()->syncWithoutDetaching([$parentId]);
        $ingredient->load(['type', 'parents', 'children']);

        return new IngredientResource($ingredient);
    }

    public function destroyRelated(Ingredient $ingredient, int $relatedId): IngredientResource
    {
        $ingredient->parents()->detach($relatedId);
        $ingredient->load(['type', 'parents', 'children']);

        return new IngredientResource($ingredient);
    }

    /**
     * @param  array<int, mixed>|null  $parentIds
     */
    private function syncParents(Ingredient $ingredient, ?array $parentIds): void
    {
        if (! is_array($parentIds)) {
            return;
        }

        $ids = collect($parentIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0 && $id !== (int) $ingredient->id)
            ->unique()
            ->values()
            ->all();

        $ingredient->parents()->sync($ids);
    }
}
