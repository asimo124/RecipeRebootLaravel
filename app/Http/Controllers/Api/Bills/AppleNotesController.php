<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppleNotesController extends BillsApiController
{
    public function destroy(Request $request): JsonResponse
    {
        return $this->legacy('deleteAppleNotes.php', $request);
    }

    public function export(Request $request): JsonResponse
    {
        return $this->legacy('apple_notes/export.php', $request);
    }

    public function index(Request $request): JsonResponse
    {
        return $this->legacy('loadAppleNotes.php', $request);
    }

    public function upload(Request $request): JsonResponse
    {
        return $this->legacy('apple_notes/upload.php', $request);
    }

    public function import(Request $request): JsonResponse
    {
        $expected = (string) config('bills.apple_notes_import_token');
        $provided = (string) $request->bearerToken();
        if ($expected === '' || $provided === '' || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->legacy('apple_notes/import.php', $request);
    }
}
