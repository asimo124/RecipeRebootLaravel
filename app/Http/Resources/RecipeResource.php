<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'is_deleted' => (bool) $this->is_deleted,
            'last_date_made' => optional($this->last_date_made)->format('Y-m-d'),
            'contains_gluten' => (bool) $this->contains_gluten,
            'image_path' => $this->image_path,
            'protein_id' => $this->protein_id,
            'recipe_style_id' => $this->recipe_style_id,
            'recipe_link' => $this->recipe_link,
            'protein' => new ProteinResource($this->whenLoaded('protein')),
            'style' => new RecipeStyleResource($this->whenLoaded('style')),
            'ingredients' => IngredientResource::collection($this->whenLoaded('ingredients')),
            'availability' => $this->when(isset($this->availability), $this->availability),
        ];
    }
}
