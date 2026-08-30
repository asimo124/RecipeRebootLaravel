<?php

namespace App\Services\Bills;

use App\Enums\Bills\SearchType;
use App\Enums\Bills\TransactionType;
use App\Models\Bills\DtTransaction;
use App\Models\Bills\DtTransactionTypeSavedSearch;
use Illuminate\Database\Eloquent\Builder;

class DisposableSavedSearchService
{
    /** @return list<array<string, mixed>> */
    public function list(): array
    {
        return DtTransactionTypeSavedSearch::query()
            ->orderBy('keyword')
            ->get()
            ->map(fn (DtTransactionTypeSavedSearch $row) => $this->serialize($row))
            ->values()
            ->all();
    }

    public function create(string $keyword, string $searchType, ?string $transactionType = null): array
    {
        $searchType = $this->normalizeSearchType($searchType);
        $transactionType = $this->normalizeTransactionType($transactionType ?? TransactionType::Covered->value);

        if ($searchType === null) {
            return ['success' => false, 'error' => 'Invalid search_type'];
        }

        if ($transactionType === null) {
            return ['success' => false, 'error' => 'Invalid transaction_type'];
        }

        $keyword = trim($keyword);
        if ($keyword === '') {
            return ['success' => false, 'error' => 'Keyword is required'];
        }

        $row = DtTransactionTypeSavedSearch::query()->create([
            'keyword' => $keyword,
            'search_type' => $searchType,
            'transaction_type' => $transactionType,
        ]);

        return ['success' => true, 'item' => $this->serialize($row)];
    }

    public function update(int $id, string $keyword, string $searchType, string $transactionType): array
    {
        $row = DtTransactionTypeSavedSearch::query()->find($id);
        if (! $row) {
            return ['success' => false, 'error' => 'Saved search not found'];
        }

        $searchType = $this->normalizeSearchType($searchType);
        $transactionType = $this->normalizeTransactionType($transactionType);

        if ($searchType === null) {
            return ['success' => false, 'error' => 'Invalid search_type'];
        }

        if ($transactionType === null) {
            return ['success' => false, 'error' => 'Invalid transaction_type'];
        }

        $keyword = trim($keyword);
        if ($keyword === '') {
            return ['success' => false, 'error' => 'Keyword is required'];
        }

        $row->keyword = $keyword;
        $row->search_type = $searchType;
        $row->transaction_type = $transactionType;
        $row->save();

        return ['success' => true, 'item' => $this->serialize($row)];
    }

    public function delete(int $id): array
    {
        $deleted = DtTransactionTypeSavedSearch::query()->where('id', $id)->delete();

        if (! $deleted) {
            return ['success' => false, 'error' => 'Saved search not found'];
        }

        return ['success' => true];
    }

    public function revertAllToDisposable(): array
    {
        $updated = DtTransaction::query()->update([
            'transaction_type' => TransactionType::Disposable->value,
        ]);

        return ['success' => true, 'updated' => $updated];
    }

    /**
     * Apply every saved search pattern against the full dt_transaction table.
     *
     * @return array{success: bool, updated: int, searches_applied: int}
     */
    public function reRunSavedSearches(): array
    {
        $searches = DtTransactionTypeSavedSearch::query()->orderBy('id')->get();
        $updated = 0;

        foreach ($searches as $search) {
            $updated += $this->applySavedSearch($search);
        }

        return [
            'success' => true,
            'updated' => $updated,
            'searches_applied' => $searches->count(),
        ];
    }

    public function applySavedSearch(DtTransactionTypeSavedSearch $search): int
    {
        $keyword = trim((string) $search->keyword);
        if ($keyword === '') {
            return 0;
        }

        $searchType = $search->search_type instanceof SearchType
            ? $search->search_type
            : SearchType::tryFrom((string) $search->search_type);

        if (! $searchType) {
            return 0;
        }

        $transactionType = $search->transaction_type instanceof TransactionType
            ? $search->transaction_type->value
            : (string) $search->transaction_type;

        $query = DtTransaction::query();
        $this->applySearchTypeFilter($query, $searchType, $keyword);

        return $query->update(['transaction_type' => $transactionType]);
    }

    private function applySearchTypeFilter(Builder $query, SearchType $searchType, string $keyword): void
    {
        match ($searchType) {
            SearchType::Regex => $query->whereRaw('name REGEXP ?', [$keyword]),
            SearchType::StartsWith => $query->where('name', 'like', $keyword.'%'),
            SearchType::EndsWith => $query->where('name', 'like', '%'.$keyword),
            SearchType::Contains => $query->where('name', 'like', '%'.$keyword.'%'),
        };
    }

    private function normalizeSearchType(string $searchType): ?string
    {
        $normalized = match ($searchType) {
            'starts_with' => SearchType::StartsWith->value,
            'ends_with' => SearchType::EndsWith->value,
            default => $searchType,
        };

        return SearchType::tryFrom($normalized)?->value;
    }

    private function normalizeTransactionType(string $transactionType): ?string
    {
        return TransactionType::tryFrom($transactionType)?->value;
    }

    /** @return array<string, mixed> */
    private function serialize(DtTransactionTypeSavedSearch $row): array
    {
        $searchType = $row->search_type instanceof SearchType
            ? $row->search_type->value
            : (string) $row->search_type;

        $transactionType = $row->transaction_type instanceof TransactionType
            ? $row->transaction_type->value
            : (string) $row->transaction_type;

        return [
            'id' => $row->id,
            'keyword' => $row->keyword,
            'search_type' => $searchType,
            'transaction_type' => $transactionType,
        ];
    }
}
