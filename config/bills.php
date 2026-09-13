<?php

return [
  /*
  |--------------------------------------------------------------------------
  | BillsSite legacy PHP root
  |--------------------------------------------------------------------------
  |
  | Absolute path to the BillsSite repo on this machine. Legacy endpoints are
  | executed from here until each action is ported to native Laravel code.
  |
  */
  'site_path' => env('BILLS_SITE_PATH', ''),

  'cors_origins' => array_values(array_filter(array_map(
      'trim',
      explode(',', (string) env('BILLS_CORS_ORIGINS', ''))
  ))),

  /*
  |--------------------------------------------------------------------------
  | Disposable income tracker CSV uploads
  |--------------------------------------------------------------------------
  |
  | Writable directory for Rocket Money CSV imports. In Docker the BillsSite
  | mount is read-only, so this defaults to Laravel storage.
  |
  */
  'disposable_data_path' => env(
      'DISPOSABLE_INCOME_TRACKER_DATA_DIR',
      storage_path('app/disposable_income_tracker')
  ),

  /*
  |--------------------------------------------------------------------------
  | Apple Notes machine import
  |--------------------------------------------------------------------------
  |
  | Long-lived bearer token for POST /api/apple_notes/import.php (cron/launchd).
  | The interactive upload at /api/apple_notes/upload.php still uses Sanctum.
  |
  */
  'apple_notes_import_token' => env('APPLE_NOTES_IMPORT_TOKEN', ''),
];
