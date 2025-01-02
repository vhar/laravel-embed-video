<?php
namespace Vhar\LaravelEmbedVideo\Providers;

use Illuminate\Support\ServiceProvider;
use Vhar\LaravelEmbedVideo\EmbedVideoService;
use Vhar\LaravelEmbedVideo\VideoHostingService;
use Vhar\LaravelEmbedVideo\Facades\VideoHosting;
use Vhar\LaravelEmbedVideo\Handlers\RutubeEmbedVideoService;
use Vhar\LaravelEmbedVideo\Handlers\VKVideoEmbedVideoService;
use Vhar\LaravelEmbedVideo\Handlers\YouTubeEmbedVideoService;


class EmbedVideoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * 
     * @return void
     */
    public function register()
    {
        $this->registerVideoHosting();
        $this->registerEmbedVideo();
    }

    /**
     * Registering main handler class
     * 
     * @return void
     */
    public function registerEmbedVideo()
    {
        $this->app->singleton('embedvideo', function () {
            return new EmbedVideoService();
        });

        $this->app->bind('embedvideo.hosting', function ($app) {
            return $app->make('embedvideo')->hosting();
        });
    }

    /**
     * Registering handler classes for video hosting
     * 
     * @return void
     */
    public function registerVideoHosting()
    {        
        $this->app->singleton('videohosting', function () {
            return new VideoHostingService();
        });

        VideoHosting::hosting('rutube', RutubeEmbedVideoService::class);
        VideoHosting::hosting('youtube', YouTubeEmbedVideoService::class);
        VideoHosting::hosting('vkvideo', VKVideoEmbedVideoService::class);
    }
}