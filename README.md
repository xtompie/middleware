# Middleware

Middleware component

## Requiments

PHP >= 8.0

## Installation

Using [composer](https://getcomposer.org/)

```
composer require xtompie/middleware
```

## Docs

One middleware is a callble `fn(mixed $input, $next): mixed`

### Private service middleware

Service without middleware:

```php
<?php

use Xtompie\Middleware\Middleware;

class TopArticlesService
{
    public function __construct(
        protected DAO $dao,
    ) {}

    public function __invoke(int $limit, int $offset): ArticleCollection
    {
        return $this->dao->query([
            "select" => "*", "from" => "article", "order" => "top DESC"
            "offset" => $offset, "limit" => $limit
        ])
            ->mapInto(Article::class)
            ->pipeInto(ArticleCollection::class);
        }
    }
}
```

Service with middleware
```php
<?php

use Xtompie\Middleware\Middleware;

class TopArticlesService
{
    public function __construct(
        protected DAO $dao,
        protected InMemoryCacheMiddleware $cache,
        protected LoggerMiddleware $logger,
    ) {}

    public function __invoke(int $limit, int $offset): ArticleCollection
    {
        return Middleware::dispatch(
            [
                $this->cache,
                $this->logger,
                fn ($args) => return $this->invoke(...$args)
            ],
            func_get_args()
        )

        return ($this->middleware)(func_get_args());
    }

    protected function invoke(int $limit, int $offset): ArticleCollection
    {
        return $this->dao->query([
            "select" => "*", "from" => "article", "order" => "top DESC"
            "offset" => $offset, "limit" => $limit
        ])
            ->mapInto(Article::class)
            ->pipeInto(ArticleCollection::class);
        }
    }
}

```

or with cached middleware chain
```php
<?php

use Xtompie\Middleware\Middleware;

class TopArticlesService
{
    public function __construct(
        protected DAO $dao,
        protected InMemoryCacheMiddleware $cache,
        protected LoggerMiddleware $logger,
        protected Middleware $middleware,
    ) {
        $this->middleware = $middleware->withMiddlewares([
            $this->cache,
            $this->logger,
            fn ($args) => return $this->invoke(...$args)
        ])
    }

    public function __invoke(int $limit, int $offset): ArticleCollection
    {
        return ($this->middleware)(func_get_args());
    }

    protected function invoke(int $limit, int $offset): ArticleCollection
    {
        return $this->dao->query([
            "select" => "*", "from" => "article", "order" => "top DESC"
            "offset" => $offset, "limit" => $limit
        ])
            ->mapInto(Article::class)
            ->pipeInto(ArticleCollection::class);
        }
    }
}

```