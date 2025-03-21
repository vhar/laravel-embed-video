<?php

namespace Vhar\LaravelEmbedVideo\Handlers;

use Illuminate\Support\Facades\Http;
use Vhar\LaravelEmbedVideo\Contracts\EmbedVideoContract;
use Vhar\LaravelEmbedVideo\EmbedData;

class RutubeEmbedVideoService implements EmbedVideoContract
{
    /**
     * Rutube url handler
     * 
     * @param string $url
     * 
     * @return EmbedData
     * 
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(string $url): EmbedData
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException("The argument must be a URL string.");
        }

        $parts = parse_url($url);
        $parts['host'] = str_replace('www.', '', $parts['host']);

        if (!in_array($parts['host'], $this->allowedDomains())) {
            throw new \InvalidArgumentException("The argument is not valid URL-address to a Rutube video.");
        }

        $path = explode('/', trim($parts['path'], '/'));
        $id = array_pop($path);

        $dataURL = sprintf("https://rutube.ru/api/video/%s/?format=json&%s", $id, $parts['query'] ?? '');

        $videoData = Http::get($dataURL)->throwUnlessStatus(200);

        $video = $videoData->json('embed_url');
        $cover = $videoData->json('thumbnail_url');

        $data = new EmbedData();

        return $data->setId($id)->setVideo($video)->setCover($cover);
    }

    /**
     * Allowed Rutube domains
     * 
     * @return array
     */
    public function allowedDomains(): array
    {
        return [
            'rutube.ru',
        ];
    }
}
