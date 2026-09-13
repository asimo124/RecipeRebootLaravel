<?php

namespace Tests\Unit;

use App\Models\Bills\AppleNote;
use PHPUnit\Framework\TestCase;

class AppleNoteTsQueryTest extends TestCase
{
    public function test_build_ts_query_joins_words_as_prefix_terms(): void
    {
        $this->assertSame('hello:* & world:*', AppleNote::buildTsQuery('hello world'));
    }

    public function test_build_ts_query_strips_punctuation(): void
    {
        $this->assertSame('meeting:*', AppleNote::buildTsQuery('meeting!!!'));
    }

    public function test_build_ts_query_returns_null_when_empty(): void
    {
        $this->assertNull(AppleNote::buildTsQuery(null));
        $this->assertNull(AppleNote::buildTsQuery(''));
        $this->assertNull(AppleNote::buildTsQuery('!!!'));
    }
}
