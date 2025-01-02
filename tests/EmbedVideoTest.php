<?php

namespace Vhar\LaravelEmbedVideo\Tests;

use Tests\TestCase;
use Vhar\LaravelEmbedVideo\EmbedData;
use Vhar\LaravelEmbedVideo\Facades\EmbedVideo;

class EmbedVideoTest extends TestCase
{
    public function test_youtube(): void
    {
        $url = env('YOUTUBE_VIDEO');

        $availableDomains = EmbedVideo::hosting('youtube')->allowedDomains();

        if (
            is_null($url)
            || filter_var($url, FILTER_VALIDATE_URL) === false
            || !in_array(parse_url($url, PHP_URL_HOST), $availableDomains)
        ) {
            $this->markTestSkipped('The YOUTUBE_VIDEO environment is not defined or contains an invalid value.');
        }

        $response = EmbedVideo::hosting('youtube')->handle($url);

        $this->assertInstanceOf(EmbedData::class, $response);
    }

    public function test_rutube(): void
    {
        $url = env('RUTUBE_VIDEO');

        $availableDomains = EmbedVideo::hosting('rutube')->allowedDomains();

        if (
            is_null($url)
            || filter_var($url, FILTER_VALIDATE_URL) === false
            || !in_array(parse_url($url, PHP_URL_HOST), $availableDomains)
        ) {
            $this->markTestSkipped('The RUTUBE_VIDEO environment is not defined or contains an invalid value.');
        }

        $response = EmbedVideo::hosting('rutube')->handle($url);

        $this->assertInstanceOf(EmbedData::class, $response);
    }

    public function test_vkvideo(): void
    {
        $url = env('VK_VIDEO');

        $availableDomains = EmbedVideo::hosting('vkvideo')->allowedDomains();

        if (
            is_null($url)
            || filter_var($url, FILTER_VALIDATE_URL) === false
            || !in_array(parse_url($url, PHP_URL_HOST), $availableDomains)
        ) {
            $this->markTestSkipped('The VK_VIDEO environment is not defined or contains an invalid value.');
        }

        $response = EmbedVideo::hosting('vkvideo')->handle($url);

        $this->assertInstanceOf(EmbedData::class, $response);
    }
}
