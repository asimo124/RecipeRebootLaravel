<?php

namespace App\Http\Controllers\Api\Bills;

use App\Http\Controllers\Controller;
use App\Services\Bills\BillsAppSettingsService;
use Illuminate\Database\Connection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function __construct(protected BillsAppSettingsService $settings)
    {
        $this->middleware('auth:sanctum');
    }

    public function testMode(Request $request): JsonResponse
    {
        if ($request->isMethod('GET')) {
            return response()->json([
                'test_mode' => $this->settings->testModeEnabled() ? 1 : 0,
            ]);
        }

        if (! $request->isMethod('POST')) {
            return response()->json(['message' => 'Method not allowed'], 405);
        }

        $enabled = 0;
        if ($request->exists('test_mode')) {
            $enabled = (int) $request->input('test_mode') ? 1 : 0;
        } elseif ($request->exists('enabled')) {
            $enabled = $request->boolean('enabled') ? 1 : 0;
        }

        $this->settings->set('test_mode', (string) $enabled);

        return response()->json([
            'test_mode' => $enabled,
            'message' => $enabled ? 'Test mode is ON.' : 'Test mode is OFF.',
        ]);
    }

    public function resetTestDb(Request $request): JsonResponse
    {
        if (! $request->isMethod('POST')) {
            return response()->json(['message' => 'Method not allowed'], 405);
        }

        $live = DB::connection('asimo124_bills');
        $test = DB::connection('asimo124_bills_test');

        $this->copyTable($live, $test, 'vnd_bills', [
            'vnd_user_id', 'vnd_bill', 'amount', 'vnd_is_auto', 'vnd_frequency_notes',
            'vnd_frequency', 'vnd_frequency_type', 'vnd_frequency_value', 'vnd_entrydate',
            'vnd_entryip', 'multiplier', 'is_future', 'is_heavy', 'watch_flag', 'end_date',
            'vnd_frequency_value_original', 'audit_regex', 'audit_keyword1', 'audit_keyword2',
            'start_date', 'can_be_multiplied_by',
        ], 'vnd_id');

        $test->table('vnd_bills')->where('end_date', '0000-00-00')->update(['end_date' => null]);
        $test->table('vnd_bills')->where('start_date', '0000-00-00')->update(['start_date' => null]);

        $this->copyTable($live, $test, 'ip_pay_period', [
            'pay_period', 'pay_period_date',
        ], 'id');

        $this->copyTable($live, $test, 'ip_pay_period_item', [
            'pay_period_id', 'disposable_amount', 'remaining_amount',
        ], 'id');

        $this->copyTable($live, $test, 'ip_upcoming_purchase', [
            'pay_period_item_id', 'title', 'description', 'cost', 'amount_to_save', 'moved',
        ], 'id');

        return response()->json([
            'success' => true,
            'message' => 'Test database has been reset from production.',
        ]);
    }

    /**
     * @param  list<string>  $columns
     */
    private function copyTable(Connection $live, Connection $test, string $table, array $columns, string $orderBy): void
    {
        $test->table($table)->truncate();

        foreach ($live->table($table)->orderBy($orderBy)->get() as $row) {
            $payload = [];
            foreach ($columns as $column) {
                $payload[$column] = $row->{$column} ?? null;
            }
            $test->table($table)->insert($payload);
        }
    }
}
