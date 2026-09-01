<?php

namespace App\Http\Requests;

use App\Models\RecipeModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRelatedIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ingredientId = (int) ($this->route('ingredient')?->id ?? $this->route('ingredient'));

        return [
            'related_ingredient_id' => [
                'required',
                'integer',
                RecipeModel::existsRule('ri_ingredient'),
                Rule::notIn([$ingredientId]),
            ],
        ];
    }
}
