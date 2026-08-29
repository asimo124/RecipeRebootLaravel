<?php

use Illuminate\Support\Facades\Route;

$legacyMap = require __DIR__.'/bills_legacy_map.php';

$publicPaths = [
    'auth/login.php',
];

foreach ($legacyMap as $legacyPath => [$controller, $action]) {
    $route = Route::any($legacyPath, [$controller, $action]);

    // MyBudgetLP authenticates with Sanctum; legacy BillsSite session tokens are not used.
    if (! in_array($legacyPath, $publicPaths, true)) {
        $route->middleware('auth:sanctum');
    }
}
