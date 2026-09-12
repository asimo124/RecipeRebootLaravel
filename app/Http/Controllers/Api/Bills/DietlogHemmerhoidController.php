<?php

namespace App\Http\Controllers\Api\Bills;

use App\Http\Controllers\Controller;
use App\Models\Bills\DlHemmerhoidLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DietlogHemmerhoidController extends Controller
{
    public function index(): JsonResponse
    {
        $items = DlHemmerhoidLog::query()
            ->orderByDesc('date_pooped')
            ->orderByDesc('id')
            ->get()
            ->map(fn (DlHemmerhoidLog $row) => $this->serialize($row))
            ->values();

        return response()->json([
            'success' => true,
            'items' => $items,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->isMethod('post')) {
            return response()->json(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        $payload = $this->validatedPayload($request);
        if ($payload instanceof JsonResponse) {
            return $payload;
        }

        $row = DlHemmerhoidLog::query()->create($payload);

        return response()->json([
            'success' => true,
            'id' => (int) $row->id,
            'item' => $this->serialize($row),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        if (! $request->isMethod('post')) {
            return response()->json(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        $payload = $this->validatedPayload($request, true);
        if ($payload instanceof JsonResponse) {
            return $payload;
        }

        $row = DlHemmerhoidLog::query()->find($payload['id']);
        if (! $row) {
            return response()->json(['success' => false, 'error' => 'Log entry not found.'], 404);
        }

        $row->fill([
            'date_pooped' => $payload['date_pooped'],
            'pain_level' => $payload['pain_level'],
            'blood_level' => $payload['blood_level'],
        ])->save();

        return response()->json([
            'success' => true,
            'id' => (int) $row->id,
            'item' => $this->serialize($row),
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        if (! $request->isMethod('post')) {
            return response()->json(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        $id = (int) $request->input('id', 0);
        if ($id <= 0) {
            return response()->json(['success' => false, 'error' => 'id is required.'], 400);
        }

        $row = DlHemmerhoidLog::query()->find($id);
        if (! $row) {
            return response()->json(['success' => false, 'error' => 'Log entry not found.'], 404);
        }

        $row->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * @return array{id?: int, date_pooped: string, pain_level: int, blood_level: int}|JsonResponse
     */
    private function validatedPayload(Request $request, bool $requireId = false): array|JsonResponse
    {
        $id = (int) $request->input('id', 0);
        $datePooped = str_replace('T', ' ', trim((string) $request->input('date_pooped', '')));
        $painLevel = (int) $request->input('pain_level', 0);
        $bloodLevel = (int) $request->input('blood_level', 0);

        if ($requireId && $id <= 0) {
            return response()->json(['success' => false, 'error' => 'id is required.'], 400);
        }

        $timestamp = $datePooped !== '' ? strtotime($datePooped) : false;
        if ($timestamp === false) {
            return response()->json(['success' => false, 'error' => 'date_pooped is required.'], 400);
        }

        if ($painLevel < 1 || $painLevel > 5 || $bloodLevel < 1 || $bloodLevel > 5) {
            return response()->json([
                'success' => false,
                'error' => 'pain_level and blood_level must be between 1 and 5.',
            ], 400);
        }

        $payload = [
            'date_pooped' => date('Y-m-d H:i:s', $timestamp),
            'pain_level' => $painLevel,
            'blood_level' => $bloodLevel,
        ];

        if ($requireId) {
            $payload['id'] = $id;
        }

        return $payload;
    }

    /**
     * @return array{id: int, date_pooped: string, pain_level: int, blood_level: int}
     */
    private function serialize(DlHemmerhoidLog $row): array
    {
        return [
            'id' => (int) $row->id,
            'date_pooped' => optional($row->date_pooped)->format('Y-m-d H:i:s') ?: (string) $row->getRawOriginal('date_pooped'),
            'pain_level' => (int) $row->pain_level,
            'blood_level' => (int) $row->blood_level,
        ];
    }
}
