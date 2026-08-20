<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLookupRequest;
use App\Http\Requests\UpdateLookupRequest;
use App\Http\Resources\ProteinResource;
use App\Models\Protein;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProteinController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProteinResource::collection(Protein::query()->orderBy('title')->get());
    }

    public function store(StoreLookupRequest $request): ProteinResource
    {
        return new ProteinResource(Protein::query()->create($request->validated()));
    }

    public function show(Protein $protein): ProteinResource
    {
        return new ProteinResource($protein);
    }

    public function update(UpdateLookupRequest $request, Protein $protein): ProteinResource
    {
        $protein->update($request->validated());

        return new ProteinResource($protein);
    }

    public function destroy(Protein $protein): JsonResponse
    {
        $protein->delete();

        return response()->json(null, 204);
    }
}
