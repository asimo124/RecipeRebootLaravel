<?php

namespace App\Http\Controllers\Api\Bills;

use App\Http\Controllers\Controller;
use App\Models\Bills\EpPurchase;
use App\Models\Bills\IpPayPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExtraPurchaseController extends Controller
{
    public function index(): JsonResponse
    {
        $payPeriods = IpPayPeriod::query()
            ->orderBy('pay_period_date')
            ->get()
            ->map(fn (IpPayPeriod $row) => $this->serializePayPeriod($row))
            ->values();

        $payPeriodsById = $payPeriods->keyBy('id');

        $items = EpPurchase::query()
            ->orderBy('purchase_date')
            ->orderBy('id')
            ->get()
            ->map(fn (EpPurchase $row) => $this->serialize($row, $payPeriodsById->get((int) $row->pay_period_id)))
            ->values();

        return response()->json([
            'success' => true,
            'items' => $items,
            'pay_periods' => $payPeriods,
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

        $row = EpPurchase::query()->create($payload);

        return response()->json([
            'success' => true,
            'id' => (int) $row->id,
            'item' => $this->serialize($row, null),
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

        $row = EpPurchase::query()->find($payload['id']);
        if (! $row) {
            return response()->json(['success' => false, 'error' => 'Purchase not found.'], 404);
        }

        $row->fill([
            'pay_period_id' => $payload['pay_period_id'],
            'title' => $payload['title'],
            'amount' => $payload['amount'],
            'purchase_date' => $payload['purchase_date'],
        ])->save();

        return response()->json([
            'success' => true,
            'id' => (int) $row->id,
            'item' => $this->serialize($row, null),
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

        $row = EpPurchase::query()->find($id);
        if (! $row) {
            return response()->json(['success' => false, 'error' => 'Purchase not found.'], 404);
        }

        $row->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * @return array{id?: int, pay_period_id: int, title: string, amount: float, purchase_date: string}|JsonResponse
     */
    private function validatedPayload(Request $request, bool $requireId = false): array|JsonResponse
    {
        $id = (int) $request->input('id', 0);
        $payPeriodId = (int) $request->input('pay_period_id', 0);
        $title = trim((string) $request->input('title', ''));
        $amount = (float) $request->input('amount', 0);
        $purchaseDate = trim((string) $request->input('purchase_date', ''));

        if ($requireId && $id <= 0) {
            return response()->json(['success' => false, 'error' => 'id is required.'], 400);
        }

        if ($title === '') {
            return response()->json(['success' => false, 'error' => 'title is required.'], 400);
        }

        $timestamp = $purchaseDate !== '' ? strtotime($purchaseDate) : false;
        if ($timestamp === false) {
            return response()->json(['success' => false, 'error' => 'purchase_date is required.'], 400);
        }

        if ($payPeriodId <= 0) {
            return response()->json(['success' => false, 'error' => 'pay_period_id is required.'], 400);
        }

        if (! IpPayPeriod::query()->whereKey($payPeriodId)->exists()) {
            return response()->json(['success' => false, 'error' => 'Pay period not found.'], 404);
        }

        $payload = [
            'pay_period_id' => $payPeriodId,
            'title' => mb_substr($title, 0, 255),
            'amount' => round($amount, 2),
            'purchase_date' => date('Y-m-d', $timestamp),
        ];

        if ($requireId) {
            $payload['id'] = $id;
        }

        return $payload;
    }

    /**
     * @param  array{id: int, label: string, start_date: string, end_date: string, days: list<string>}|null  $payPeriod
     * @return array{id: int, pay_period_id: int, title: string, amount: float, purchase_date: string, pay_period_label: string, pay_period_start_date: string}
     */
    private function serialize(EpPurchase $row, ?array $payPeriod): array
    {
        return [
            'id' => (int) $row->id,
            'pay_period_id' => (int) $row->pay_period_id,
            'title' => (string) $row->title,
            'amount' => round((float) $row->amount, 2),
            'purchase_date' => optional($row->purchase_date)->format('Y-m-d')
                ?: substr((string) $row->getRawOriginal('purchase_date'), 0, 10),
            'pay_period_label' => (string) ($payPeriod['label'] ?? ''),
            'pay_period_start_date' => (string) ($payPeriod['start_date'] ?? ''),
        ];
    }

    /**
     * Pay periods are semi-monthly: the 1st runs through the 14th, the 15th
     * through the end of the month. Mirrors IpPayPeriod::updatePayPeriods().
     *
     * @return array{id: int, label: string, start_date: string, end_date: string, days: list<string>}
     */
    private function serializePayPeriod(IpPayPeriod $row): array
    {
        $start = CarbonImmutable::parse((string) $row->getRawOriginal('pay_period_date'))->startOfDay();
        $end = $start->day < 15
            ? $start->startOfMonth()->addDays(13)
            : $start->endOfMonth()->startOfDay();

        $days = [];
        for ($day = $start; $day->lessThanOrEqualTo($end); $day = $day->addDay()) {
            $days[] = $day->format('Y-m-d');
        }

        return [
            'id' => (int) $row->id,
            'label' => (string) $row->pay_period,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'days' => $days,
        ];
    }
}
