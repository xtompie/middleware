<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Middleware\Middleware;

class MiddlewareTest extends TestCase
{
    public function testChain()
    {
        // given

        $a = function ($input, $next) { return 'a' . $next($input); };
        $b = function ($input, $next) { return $next($input) . 'b'; };
        $c = function ($input, $next) { return $input . $input; };
        $d = function ($input, $next) { return 'd' . $next($input); };
        $middleware = new Middleware([$a, $b, $c, $d]);

        // when
        $result = $middleware('x');

        // then
        $this->assertSame('axxb', $result);
    }
}
