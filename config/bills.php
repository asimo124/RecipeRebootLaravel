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
];
