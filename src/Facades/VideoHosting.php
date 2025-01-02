<?php

namespace Vhar\LaravelEmbedVideo\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * @method static void hosting(string $alias, string $class)
 * 
 * @see \Vhar\LaravelEmbedVideo\VideoHostingService
 */
class VideoHosting extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'videohosting';
    }
}
