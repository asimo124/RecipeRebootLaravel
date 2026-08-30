<?php

namespace App\Http\Controllers\Api\Bills;

use App\Services\Bills\DisposableTrackerService;
use App\Services\Bills\LegacyBillsScriptRunner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisposableController extends BillsApiController
{
    public function __construct(
        LegacyBillsScriptRunner $legacyRunner,
        protected DisposableTrackerService $trackerService,
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
        return $this->legacy('disposable/upload_import.php', $request);
    }
    public function uploadPreview(Request $request): JsonResponse
    {
        return $this->legacy('disposable/upload_preview.php', $request);
    }
}
