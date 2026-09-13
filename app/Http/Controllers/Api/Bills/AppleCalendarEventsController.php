<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppleCalendarEventsController extends BillsApiController
{
    public function destroy(Request $request): JsonResponse
    {
        return $this->legacy('deleteAppleCalendarEvents.php', $request);
    }

    public function export(Request $request): JsonResponse
    {
        return $this->legacy('apple_calendar/export.php', $request);
    }

    public function index(Request $request): JsonResponse
    {
        return $this->legacy('loadAppleCalendarEvents.php', $request);
    }

    public function upload(Request $request): JsonResponse
    {
        return $this->legacy('apple_calendar/upload.php', $request);
    }

    public function import(Request $request): JsonResponse
    {
        $expected = (string) config('bills.apple_calendar_import_token');
        $provided = (string) $request->bearerToken();
        if ($expected === '' || $provided === '' || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->legacy('apple_calendar/import.php', $request);
    }
}
