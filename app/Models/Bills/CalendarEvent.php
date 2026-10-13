<?php

namespace App\Models\Bills;

class CalendarEvent extends BillsModel
{
    protected $table = 'calendar_events';

    protected $fillable = [
        'event_key',
        'calendar_id',
        'calendar_name',
        'uid',
        'summary',
        'description',
        'location',
        'status',
        'start_utc',
        'end_utc',
        'all_day',
        'is_recurring',
        'rrule',
        'last_modified_utc',
        'sync_run',
        'updated_at',
    ];

    protected $casts = [
        'all_day' => 'integer',
        'is_recurring' => 'integer',
        'sync_run' => 'integer',
        'start_utc' => 'datetime',
        'end_utc' => 'datetime',
        'last_modified_utc' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
