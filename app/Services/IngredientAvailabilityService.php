<?php

namespace App\Services;

use App\Models\HomeInventory;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Support\Collection;

class IngredientAvailabilityService
{
    /**
     * Return pantry coverage for a recipe.
     *
     * @return array{ready: bool, total: int, available: int, missing: array<int, array{id:int,title:string}>}
     */
    public function forRecipe(Recipe $recipe): array
    {
        $recipe->loadMissing('ingredients');
        $inventoryIds = HomeInventory::query()->pluck('ingredient_id')->unique()->all();
        $inventorySet = array_fill_keys($inventoryIds, true);

        $missing = [];
        $available = 0;

        foreach ($recipe->ingredients as $ingredient) {
            if ($this->isSatisfied($ingredient, $inventorySet)) {
                $available++;
            } else {
                $missing[] = [
                    'id' => $ingredient->id,
                    'title' => $ingredient->title,
                ];
            }
        }

        $total = $recipe->ingredients->count();

        return [
            'ready' => $total > 0 && count($missing) === 0,
            'total' => $total,
            'available' => $available,
            'missing' => $missing,
        ];
    }

    /**
     * @param  array<int, bool>  $inventorySet
     */
    public function isSatisfied(Ingredient $ingredient, array $inventorySet): bool
    {
        foreach ($this->ancestorIds($ingredient) as $id) {
            if (isset($inventorySet[$id])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ingredient itself + parent chain via ri_related_ingredient.
     *
     * @return Collection<int, int>
     */
    public function ancestorIds(Ingredient $ingredient): Collection
    {
        $visited = collect([$ingredient->id]);
        $queue = [$ingredient->id];

        while ($queue) {
            $currentId = array_shift($queue);
            $current = Ingredient::query()->with('parents:id')->find($currentId);
            if (! $current) {
                continue;
            }

            foreach ($current->parents as $parent) {
                if ($visited->contains($parent->id)) {
                    continue;
                }
                $visited->push($parent->id);
                $queue[] = $parent->id;
            }
        }

        return $visited;
    }
}
