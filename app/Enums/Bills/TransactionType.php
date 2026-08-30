<?php

namespace App\Enums\Bills;

enum TransactionType: string
{
    case Disposable = 'disposable';
    case Covered = 'covered';
    case ImpulseBuy = 'impulse buy';
}
