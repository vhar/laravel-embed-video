<?php

namespace Vhar\LaravelEmbedVideo\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * @method static \Vhar\LaravelEmbedVideo\Contracts\EmbedVideoContract hosting(string $alias)
 * @method statig array handle(string $url)
 * 
 * @see \Vhar\LaravelEmbedVideo\EmbedVideoService
 */
class EmbedVideo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'embedvideo';
    }
}
