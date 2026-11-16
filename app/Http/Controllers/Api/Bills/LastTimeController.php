<?php

namespace App\Http\Controllers\Api\Bills;

use App\Http\Controllers\Controller;
use App\Models\ApiDb\LtcItem;
use App\Models\ApiDb\LtcItemUsedHistory;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * "Last Time I" / "When Did I Last?" for MyBudgetLP.
 *
 * Native port of api.hawleywebdesign.com's LastTimeController (Yii), reading the
 * same api_db ltc_item / ltc_item_used_history tables. Behaviour is kept the same,
 * including the Methylfolate series quirk below.
 */
class LastTimeController extends Controller
{
    /**
     * Logging this item also schedules it every 2 days for the next 180 doses,
     * and removing any one of its entries removes the whole series.
     * Mirrors the hard-coded behaviour of the Yii API.
     */
    private const SERIES_ITEM_ID = 157;

    private const SERIES_REPEAT_COUNT = 180;

    private const SERIES_REPEAT_EVERY_DAYS = 2;

    /** The calendar is centred this many days in the future (same as the Yii API). */
    private const ANCHOR_DAYS_AHEAD = 91;

    /** Whole weeks shown before the anchor date's week. */
    private const WEEKS_BEFORE_ANCHOR = 17;

    public function items(): JsonResponse
    {
        $items = LtcItem::query()
            ->orderBy('title')
            ->get(['id', 'title', 'color'])
            ->map(fn (LtcItem $item) => $this->serializeItem($item))
            ->values();

        return response()->json(['success' => true, 'items' => $items]);
    }

    /**
     * 18 Sunday-to-Saturday weeks ending the week of (today + 91 days - days_back).
     */
    public function calendar(Request $request): JsonResponse
    {
        $daysBack = max(0, (int) $request->input('days_back', $request->input('daysBack', 0)));

        $today = CarbonImmutable::today();
        $anchor = $today->addDays(self::ANCHOR_DAYS_AHEAD - $daysBack);
        $weekStart = $anchor->subDays($anchor->dayOfWeek); // Sunday
        $start = $weekStart->subWeeks(self::WEEKS_BEFORE_ANCHOR);
        $end = $weekStart->addDays(6); // Saturday

        $usedByDate = LtcItemUsedHistory::query()
            ->join('ltc_item', 'ltc_item.id', '=', 'ltc_item_used_history.item_id')
            ->whereBetween('ltc_item_used_history.date_used', [$start->toDateString(), $end->toDateString()])
            ->orderBy('ltc_item_used_history.id')
            ->get([
                'ltc_item_used_history.id',
                'ltc_item_used_history.item_id',
                'ltc_item_used_history.date_used',
                'ltc_item.title',
                'ltc_item.color',
            ])
            ->groupBy(fn (LtcItemUsedHistory $row) => $row->date_used->toDateString());

        $weeks = [];
        $first = true;
        for ($day = $start; $day->lte($end); $day = $day->addDay()) {
            if ($day->dayOfWeek === 0) {
                $weeks[] = ['days' => []];
            }

            $date = $day->toDateString();
            $weeks[array_key_last($weeks)]['days'][] = [
                'date' => $date,
                'day_of_week' => $day->dayOfWeek,
                'day_of_month' => $day->day,
                // Month label on the 1st of each month and on the first cell.
                'month_label' => ($first || $day->day === 1) ? $day->format('M') : null,
                'is_today' => $day->equalTo($today),
                'items' => ($usedByDate->get($date) ?? collect())
                    ->map(fn (LtcItemUsedHistory $row) => [
                        'id' => (int) $row->id,
                        'item_id' => (int) $row->item_id,
                        'title' => (string) $row->title,
                        'color' => (string) $row->color,
                    ])
                    ->values(),
            ];
            $first = false;
        }

        return response()->json([
            'success' => true,
            'days_back' => $daysBack,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'today' => $today->toDateString(),
            'weeks' => $weeks,
        ]);
    }

    /** Every date an item was used, newest first. */
    public function search(Request $request): JsonResponse
    {
        $itemId = (int) $request->input('item_id', 0);
        $item = $itemId > 0 ? LtcItem::query()->find($itemId) : null;
        if (! $item) {
            return response()->json(['success' => false, 'error' => 'Item not found'], 404);
        }

        $history = LtcItemUsedHistory::query()
            ->where('item_id', $itemId)
            ->orderByDesc('date_used')
            ->orderByDesc('id')
            ->get(['id', 'date_used'])
            ->map(fn (LtcItemUsedHistory $row) => [
                'id' => (int) $row->id,
                'date_used' => $row->date_used->toDateString(),
            ])
            ->values();

        return response()->json([
            'success' => true,
            'item' => $this->serializeItem($item),
            'history' => $history,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->isMethod('post')) {
            return response()->json(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        $itemId = (int) $request->input('item_id', 0);
        $rawDate = trim((string) $request->input('date_used', ''));

        if ($itemId <= 0 || $rawDate === '') {
            return response()->json(['success' => false, 'error' => 'Item and date are required.'], 422);
        }
        if (! LtcItem::query()->whereKey($itemId)->exists()) {
            return response()->json(['success' => false, 'error' => 'Item not found.'], 404);
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDate)) {
            return response()->json(['success' => false, 'error' => 'Date must be YYYY-MM-DD.'], 422);
        }
        try {
            $date = CarbonImmutable::createFromFormat('!Y-m-d', $rawDate);
        } catch (\Throwable) {
            $date = false;
        }
        if (! $date || $date->toDateString() !== $rawDate) {
            return response()->json(['success' => false, 'error' => 'Date is not valid.'], 422);
        }

        $rows = [['item_id' => $itemId, 'date_used' => $date->toDateString()]];
        if ($itemId === self::SERIES_ITEM_ID) {
            for ($i = 1; $i <= self::SERIES_REPEAT_COUNT; $i++) {
                $rows[] = [
                    'item_id' => $itemId,
                    'date_used' => $date->addDays($i * self::SERIES_REPEAT_EVERY_DAYS)->toDateString(),
                ];
            }
        }

        $firstId = DB::connection('api_db')->transaction(function () use ($rows) {
            $first = LtcItemUsedHistory::query()->create($rows[0]);
            if (count($rows) > 1) {
                LtcItemUsedHistory::query()->insert(array_slice($rows, 1));
            }

            return (int) $first->id;
        });

        return response()->json([
            'success' => true,
            'id' => $firstId,
            'created' => count($rows),
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        if (! $request->isMethod('post')) {
            return response()->json(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        $id = (int) $request->input('id', 0);
        $row = $id > 0 ? LtcItemUsedHistory::query()->find($id) : null;
        if (! $row) {
            return response()->json(['success' => false, 'error' => 'Entry not found.'], 404);
        }

        $deleted = $row->item_id === self::SERIES_ITEM_ID
            ? LtcItemUsedHistory::query()->where('item_id', self::SERIES_ITEM_ID)->delete()
            : (int) $row->delete();

        return response()->json(['success' => true, 'deleted' => $deleted]);
    }

    /** @return array{id:int,title:string,color:string,is_series:bool} */
    private function serializeItem(LtcItem $item): array
    {
        return [
            'id' => (int) $item->id,
            'title' => (string) $item->title,
            'color' => (string) $item->color,
            // UI warns that removing one entry of a series removes all of them.
            'is_series' => (int) $item->id === self::SERIES_ITEM_ID,
        ];
    }
}
