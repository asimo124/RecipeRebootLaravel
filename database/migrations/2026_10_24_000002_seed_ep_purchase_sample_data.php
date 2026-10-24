<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sample Extra Purchases data for the three most recent pay periods.
 *
 * This is demo data, not real spending. Remove it with
 * `php artisan migrate:rollback`, or delete the rows from the UI.
 */
return new class extends Migration
{
    /** @var list<string> */
    private array $connections = ['asimo124_bills', 'asimo124_bills_test'];

    /**
     * Purchases per pay period, newest period first. Each row is
     * [days after the pay period start, title, amount].
     *
     * Offsets stay within 0-9 so they land inside a 14-day pay period and
     * never run past today in the current one. Repeated offsets are
     * deliberate: they give the per-day drill-down something to break apart.
     *
     * @var list<list<array{0: int, 1: string, 2: float}>>
     */
    private array $samples = [
        [
            [1, 'Cold brew maker', 42.99],
            [1, 'Bag of fancy coffee beans', 18.50],
            [3, 'Impulse paperback', 14.25],
            [5, 'Takeout wings', 28.75],
            [8, 'Phone case', 21.00],
            [9, 'Streaming movie rental', 6.99],
        ],
        [
            [0, 'Energy drink 12-pack', 24.99],
            [2, 'Mechanical keyboard keycaps', 45.00],
            [2, 'Desk succulent', 12.00],
            [2, 'Vending machine snack run', 9.75],
            [4, 'Movie tickets', 32.50],
            [6, 'Hoodie on clearance', 54.99],
            [7, 'Bluetooth tracker 2-pack', 29.99],
            [9, 'Candy and soda', 7.25],
        ],
        [
            [2, 'Vinyl record', 27.99],
            [4, 'Cable organizer gadget', 15.49],
            [4, 'Fast food lunch', 11.80],
            [7, 'Video game on sale', 19.99],
        ],
    ];

    public function up(): void
    {
        foreach ($this->connections as $connection) {
            if (! $this->tablesExist($connection)) {
                continue;
            }

            $periods = $this->recentPayPeriods($connection);
            if ($periods === []) {
                continue;
            }

            $periodIds = array_map(static fn ($period) => (int) $period->id, $periods);

            // Re-running against the same pay periods would duplicate everything.
            $alreadySeeded = DB::connection($connection)
                ->table('ep_purchase')
                ->whereIn('pay_period_id', $periodIds)
                ->whereIn('title', $this->sampleTitles())
                ->exists();

            if ($alreadySeeded) {
                continue;
            }

            $rows = [];
            foreach ($periods as $index => $period) {
                foreach ($this->samples[$index] ?? [] as [$dayOffset, $title, $amount]) {
                    $rows[] = [
                        'pay_period_id' => (int) $period->id,
                        'title' => $title,
                        'amount' => $amount,
                        'purchase_date' => $this->purchaseDate($period, $dayOffset),
                    ];
                }
            }

            if ($rows !== []) {
                DB::connection($connection)->table('ep_purchase')->insert($rows);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->connections as $connection) {
            if (! Schema::connection($connection)->hasTable('ep_purchase')) {
                continue;
            }

            DB::connection($connection)
                ->table('ep_purchase')
                ->whereIn('title', $this->sampleTitles())
                ->delete();
        }
    }

    private function tablesExist(string $connection): bool
    {
        return Schema::connection($connection)->hasTable('ep_purchase')
            && Schema::connection($connection)->hasTable('ip_pay_period');
    }

    /**
     * The three most recent pay periods that have already started, newest
     * first. Read from the table rather than hard-coded so this still lands
     * on real pay periods whenever it runs.
     *
     * @return list<object>
     */
    private function recentPayPeriods(string $connection): array
    {
        return DB::connection($connection)
            ->table('ip_pay_period')
            ->whereDate('pay_period_date', '<=', CarbonImmutable::now()->toDateString())
            ->orderByDesc('pay_period_date')
            ->limit(count($this->samples))
            ->get()
            ->all();
    }

    /**
     * Pay periods are semi-monthly: the 1st runs through the 14th, the 15th
     * through the end of the month. Dates are clamped so demo purchases never
     * land past the period end or in the future.
     */
    private function purchaseDate(object $period, int $dayOffset): string
    {
        $start = CarbonImmutable::parse($period->pay_period_date)->startOfDay();
        $end = $start->day < 15
            ? $start->startOfMonth()->addDays(13)
            : $start->endOfMonth()->startOfDay();

        $today = CarbonImmutable::now()->startOfDay();
        if ($end->greaterThan($today)) {
            $end = $today;
        }

        $date = $start->addDays($dayOffset);

        return ($date->greaterThan($end) ? $end : $date)->format('Y-m-d');
    }

    /** @return list<string> */
    private function sampleTitles(): array
    {
        $titles = [];
        foreach ($this->samples as $periodSamples) {
            foreach ($periodSamples as [, $title]) {
                $titles[] = $title;
            }
        }

        return $titles;
    }
};
