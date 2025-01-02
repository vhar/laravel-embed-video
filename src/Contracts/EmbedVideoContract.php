<?php

namespace Vhar\LaravelEmbedVideo\Contracts;

use Vhar\LaravelEmbedVideo\EmbedData;

interface EmbedVideoContract
{
    /**
     * Handling Video URL
     * 
     * @param string $url
     * 
     * @return EmbedData
     */
    public function handle(string $url): EmbedData;

    /**
     * Allowed domains in input video URL
     * 
     * @return array
     */
    public function allowedDomains(): array;
}
