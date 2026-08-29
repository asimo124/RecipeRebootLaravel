<?php

namespace App\Http\Controllers\Api\Bills;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TitleMatchController extends BillsApiController
{
    public function destroy(Request $request): JsonResponse
    {
        return $this->legacy('removeTitleMatch.php', $request);
    }
    public function index(Request $request): JsonResponse
    {
        return $this->legacy('loadTitleMatches.php', $request);
    }
    public function store(Request $request): JsonResponse
    {
        return $this->legacy('insertTitleMatch.php', $request);
    }
}
