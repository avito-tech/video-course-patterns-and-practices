<?php

declare(strict_types=1);

namespace App;

use DIContainer\CachedContainer;
use Symfony\Component\HttpFoundation\Request;

class Bootstrap
{
    public static function init()
    {
        return (new CachedContainer())
            ->get('App\Kernel')
            ->handle(
                Request::createFromGlobals()
            );
    }
}
