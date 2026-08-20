<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRelatedIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'related_ingredient_id' => ['required', 'integer', 'exists:ri_ingredient,id', 'different:ingredient'],
        ];
    }
}
