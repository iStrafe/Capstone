<?php

namespace Tests\Unit;

use App\Support\UploadLimit;
use PHPUnit\Framework\TestCase;

class UploadLimitTest extends TestCase
{
    public function test_php_ini_sizes_are_read_as_bytes(): void
    {
        $this->assertSame(2 * 1024 * 1024, UploadLimit::bytes('2M'));
        $this->assertSame(512 * 1024, UploadLimit::bytes('512k'));
        $this->assertSame(1024 ** 3, UploadLimit::bytes('1G'));
        $this->assertSame(8000, UploadLimit::bytes('8000'));
    }

    public function test_zero_or_empty_means_no_limit(): void
    {
        $this->assertSame(PHP_INT_MAX, UploadLimit::bytes('0'));
        $this->assertSame(PHP_INT_MAX, UploadLimit::bytes(''));
    }

    public function test_sizes_are_labelled_for_messages(): void
    {
        $this->assertSame('2 MB', UploadLimit::label(2 * 1024 * 1024));
        $this->assertSame('1.5 MB', UploadLimit::label(1536 * 1024));
        $this->assertSame('800 KB', UploadLimit::label(800 * 1024));
    }
}
