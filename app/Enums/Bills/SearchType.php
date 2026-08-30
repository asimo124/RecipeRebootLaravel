<?php

namespace App\Enums\Bills;

enum SearchType: string
{
    case Contains = 'contains';
    case StartsWith = 'starts with';
    case EndsWith = 'ends with';
    case Regex = 'regex';
}
