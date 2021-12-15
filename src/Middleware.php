<?php

declare(strict_types=1);

namespace Xtompie\Middleware;

class Middleware
{
    public static function dispatch(array $middlewares, $input): mixed
    {
        return (new static($middlewares))->__invoke($input);
    }

    protected $chain;

    public function __construct(
        protected array $middlewares = []
    ){
        $this->chain($middlewares);
    }

    public function __invoke(mixed $input): mixed
    {
        return ($this->chain)($input);
    }

    public function withMiddlewares(array $middlewares)
    {
        $new = clone $this;
        $new->chain($middlewares);
        return $new;
    }

    protected function chain(array $middlewares)
    {
        $chain = fn () => null;
        while ($middleware = array_pop($middlewares)) {
            $chain = fn ($input) => $middleware($input, $chain);
        }
        $this->chain = $chain;
    }
}
