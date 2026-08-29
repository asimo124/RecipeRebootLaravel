<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisposableController extends BillsApiController
{
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
        return $this->legacy('loadDisposableTransactions.php', $request);
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
