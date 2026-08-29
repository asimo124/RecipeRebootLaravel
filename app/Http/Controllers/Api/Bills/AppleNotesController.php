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
}
