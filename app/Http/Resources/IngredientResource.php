<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'ingredient_type_id' => $this->ingredient_type_id,
            'type' => new IngredientTypeResource($this->whenLoaded('type')),
            'recipe_count' => (int) ($this->recipes_count ?? 0),
            'parents' => IngredientResource::collection($this->whenLoaded('parents')),
            'children' => IngredientResource::collection($this->whenLoaded('children')),
        ];
    }
}
