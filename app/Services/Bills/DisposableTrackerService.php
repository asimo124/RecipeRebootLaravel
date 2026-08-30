<?php

namespace App\Services\Bills;

use App\Enums\Bills\TransactionType;
use App\Models\Bills\DtTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DisposableTrackerService
{
    /** @return list<string> */
    public static function transactionTypeValues(): array
    {
        return array_map(
            static fn (TransactionType $type) => $type->value,
            TransactionType::cases()
        );
    }

    /** @param array<string, mixed> $filters */
    public function listTransactions(array $filters): array
    {
        $query = DtTransaction::query()
            ->join('dt_transaction_category as tc', 'dt_transaction.transaction_category_id', '=', 'tc.id')
            ->where('dt_transaction.amount', '>', 0)
            ->select([
                'dt_transaction.id',
                'dt_transaction.name',
                'dt_transaction.amount',
                'dt_transaction.transaction_date',
                'dt_transaction.paycheck_date',
                'dt_transaction.transaction_type',
                'tc.title as category_name',
            ]);

        if (! empty($filters['start_paycheck_date'])) {
            $query->where(
                'dt_transaction.transaction_date',
                '>=',
                (string) $filters['start_paycheck_date']
            );
        }

        if (! empty($filters['end_paycheck_date'])) {
            $query->where(
                'dt_transaction.transaction_date',
                '<=',
                (string) $filters['end_paycheck_date']
            );
        }

        $types = $this->normalizeTransactionTypes($filters['transaction_types'] ?? null);
        $query->whereIn('dt_transaction.transaction_type', $types);

        $this->applyKeywordFilter($query, $filters, 1);
        $this->applyKeywordFilter($query, $filters, 2);

        $items = $query
            ->orderBy('dt_transaction.transaction_date')
            ->orderBy('dt_transaction.name')
            ->get()
            ->map(static function ($row) {
                $transactionType = $row->transaction_type instanceof TransactionType
                    ? $row->transaction_type->value
                    : (string) $row->transaction_type;

                return [
                    'id' => $row->id,
                    'name' => $row->name,
                    'amount' => number_format((float) $row->amount, 2, '.', ''),
                    'transaction_date' => $row->transaction_date,
                    'transaction_date_display' => Carbon::parse($row->transaction_date)->format('m/d'),
                    'transaction_type' => $transactionType,
                    'paycheck_date' => $row->paycheck_date,
                    'category_name' => $row->category_name,
                ];
            })
            ->values()
            ->all();

        return ['items' => $items];
    }

    /** @param list<int> $ids */
    public function bulkUpdateTransactionType(array $ids, string $transactionType): array
    {
        if (! in_array($transactionType, self::transactionTypeValues(), true)) {
            return ['success' => false, 'error' => 'Invalid transaction_type'];
        }

        $updated = DtTransaction::query()
            ->whereIn('id', $ids)
            ->update(['transaction_type' => $transactionType]);

        return ['success' => true, 'updated' => $updated];
    }

    /** @param mixed $types */
    private function normalizeTransactionTypes($types): array
    {
        if (is_string($types)) {
            $types = array_filter(array_map('trim', explode(',', $types)));
        }

        if (! is_array($types) || $types === []) {
            return [TransactionType::Disposable->value];
        }

        $valid = self::transactionTypeValues();

        return array_values(array_intersect($types, $valid));
    }

    /** @param array<string, mixed> $filters */
    private function applyKeywordFilter(Builder $query, array $filters, int $number): void
    {
        $keyword = trim((string) ($filters["keyword{$number}"] ?? ''));
        if ($keyword === '') {
            return;
        }

        $mode = (string) ($filters["keyword{$number}_mode"] ?? 'includes');
        $match = (string) ($filters["keyword{$number}_match"] ?? 'contains');

        if ($match === 'regex') {
            if ($mode !== 'includes') {
                return;
            }

            $query->whereRaw('dt_transaction.name REGEXP ?', [$keyword]);

            return;
        }

        $likePattern = match ($match) {
            'starts_with' => $keyword.'%',
            'ends_with' => '%'.$keyword,
            default => '%'.$keyword.'%',
        };

        if ($mode === 'excludes') {
            $query->where('dt_transaction.name', 'not like', $likePattern);
        } else {
            $query->where('dt_transaction.name', 'like', $likePattern);
        }
    }
}
