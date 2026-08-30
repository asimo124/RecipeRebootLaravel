<?php

namespace App\Http\Controllers\Api\Bills;

use App\Services\Bills\DisposableSavedSearchService;
use App\Services\Bills\DisposableTrackerService;
use App\Services\Bills\LegacyBillsScriptRunner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisposableController extends BillsApiController
{
    public function __construct(
        LegacyBillsScriptRunner $legacyRunner,
        protected DisposableTrackerService $trackerService,
        protected DisposableSavedSearchService $savedSearchService,
    ) {
        parent::__construct($legacyRunner);
    }

    public function accountNames(Request $request): JsonResponse
    {
        return $this->legacy('loadDisposableAccountNames.php', $request);
    }

    public function accountNumbers(Request $request): JsonResponse
    {
        return $this->legacy('loadDisposableAccountNumbers.php', $request);
    }

    public function accountTypes(Request $request): JsonResponse
    {
        return $this->legacy('loadDisposableAccountTypes.php', $request);
    }

    public function categoryNames(Request $request): JsonResponse
    {
        return $this->legacy('loadDisposableCategoryNames.php', $request);
    }

    public function chartData(Request $request): JsonResponse
    {
        return $this->legacy('loadDisposableTransactionsChartData.php', $request);
    }

    public function chartDataCategory(Request $request): JsonResponse
    {
        return $this->legacy('loadDisposableTransactionsChartDataCategory.php', $request);
    }

    public function chartDataDay(Request $request): JsonResponse
    {
        return $this->legacy('loadDisposableTransactionsChartDataDay.php', $request);
    }

    public function institutionNames(Request $request): JsonResponse
    {
        return $this->legacy('loadDisposableInstitutionNames.php', $request);
    }

    public function transactions(Request $request): JsonResponse
    {
        return response()->json($this->trackerService->listTransactions($request->all()));
    }

    public function bulkUpdateTransactionTypes(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'transaction_type' => ['required', 'string'],
        ]);

        return response()->json($this->trackerService->bulkUpdateTransactionType(
            $validated['ids'],
            $validated['transaction_type'],
        ));
    }

    public function savedSearches(): JsonResponse
    {
        return response()->json(['items' => $this->savedSearchService->list()]);
    }

    public function storeSavedSearch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'keyword' => ['required', 'string'],
            'search_type' => ['required', 'string'],
            'transaction_type' => ['nullable', 'string'],
        ]);

        $result = $this->savedSearchService->create(
            $validated['keyword'],
            $validated['search_type'],
            $validated['transaction_type'] ?? null,
        );

        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }

    public function updateSavedSearch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'keyword' => ['required', 'string'],
            'search_type' => ['required', 'string'],
            'transaction_type' => ['required', 'string'],
        ]);

        $result = $this->savedSearchService->update(
            $validated['id'],
            $validated['keyword'],
            $validated['search_type'],
            $validated['transaction_type'],
        );

        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }

    public function destroySavedSearch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $result = $this->savedSearchService->delete($validated['id']);

        return response()->json($result, ($result['success'] ?? false) ? 200 : 404);
    }

    public function revertAllToDisposable(): JsonResponse
    {
        return response()->json($this->savedSearchService->revertAllToDisposable());
    }

    public function reRunSavedSearches(): JsonResponse
    {
        return response()->json($this->savedSearchService->reRunSavedSearches());
    }

    public function updateAllNotCovered(Request $request): JsonResponse
    {
        return $this->legacy('updateAllNotCovered.php', $request);
    }

    public function updateTransactionCovered(Request $request): JsonResponse
    {
        return $this->legacy('updateDisposableTransactionCovered.php', $request);
    }

    public function uploadImport(Request $request): JsonResponse
    {
        $response = $this->legacy('disposable/upload_import.php', $request);
        $payload = $response->getData(true);

        if (is_array($payload) && ($payload['success'] ?? false)) {
            $applied = $this->savedSearchService->reRunSavedSearches();
            $payload['saved_searches_applied'] = $applied['searches_applied'] ?? 0;
            $payload['saved_searches_updated'] = $applied['updated'] ?? 0;

            return response()->json($payload, $response->getStatusCode());
        }

        return $response;
    }

    public function uploadPreview(Request $request): JsonResponse
    {
        return $this->legacy('disposable/upload_preview.php', $request);
    }
}
