<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushNotificationController extends BillsApiController
{
    public function assignSchedule(Request $request): JsonResponse
    {
        return $this->legacy('push_notifications/assign_schedule.php', $request);
    }
    public function destroy(Request $request): JsonResponse
    {
        return $this->legacy('push_notifications/delete.php', $request);
    }
    public function inc(Request $request): JsonResponse
    {
        return $this->legacy('push_notifications/inc.php', $request);
    }
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('push_notifications/list.php', $request);
    }
    public function scheduleDestroy(Request $request): JsonResponse
    {
        return $this->legacy('push_notifications/schedule_delete.php', $request);
    }
    public function scheduleShow(Request $request): JsonResponse
    {
        return $this->legacy('push_notifications/schedule_get.php', $request);
    }
    public function scheduleStore(Request $request): JsonResponse
    {
        return $this->legacy('push_notifications/schedule_create.php', $request);
    }
    public function scheduleUpdate(Request $request): JsonResponse
    {
        return $this->legacy('push_notifications/schedule_update.php', $request);
    }
    public function show(Request $request): JsonResponse
    {
        return $this->legacy('push_notifications/get.php', $request);
    }
    public function store(Request $request): JsonResponse
    {
        return $this->legacy('push_notifications/create.php', $request);
    }
    public function update(Request $request): JsonResponse
    {
        return $this->legacy('push_notifications/update.php', $request);
    }
}
