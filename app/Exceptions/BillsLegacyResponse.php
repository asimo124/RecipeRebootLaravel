<?php

namespace App\Exceptions;

use Exception;

class BillsLegacyResponse extends Exception
{
    public function __construct(
        public readonly mixed $payload,
        public readonly int $status = 200,
    ) {
        parent::__construct('Bills legacy API response');
    }
}
