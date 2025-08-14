<?php

namespace Tests\Unit;

use App\Jobs\ProcessOrder;
use PHPUnit\Framework\TestCase;


class ProcessOrderJobConfigTest extends TestCase
{
    public function tiene_tries_y_backoff_definidos()
    {
        $job = new ProcessOrder(['id' => 'X']);
        $this->assertSame(5, $job->tries);
        $this->assertSame([60, 300, 600, 1800], $job->backoff);
    }
}
