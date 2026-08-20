<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttributeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $attributes = Attribute::query()
            ->orderByDesc('severity_level')
            ->orderBy('title')
            ->get();

        return AttributeResource::collection($attributes);
    }
}
