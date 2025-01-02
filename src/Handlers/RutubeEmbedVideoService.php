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
     * @return array
     * 
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(string $url): EmbedData
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException("The argument must be a URL.");
        }

        Http::get($url)->throwUnlessStatus(200);

        $parts = parse_url($url);
        $parts['host'] = str_replace('www.', '', $parts['host']);

        if (!in_array($parts['host'], $this->allowedDomains())) {
            throw new \InvalidArgumentException("The argument is not valid URL-address to a Rutube video.");
        }
        
        $path = explode('/', ltrim($parts['path'], '/'));

        switch($path[0]) {
            case 'video':
                if ($path[1] === 'private') {
                    $id = $path[2];
                } else {
                    $id = $path[1];
                }
                break;
            case 'play':
                $id = $path[2];
                break;
            default:
                $id = 'f';
        }

        $video = 'https://rutube.ru/play/embed/' . $id;
        $url = 'https://rutube.ru/video/' . $id;

        $cover = $this->getCover($url);

        return EmbedData::create($id, $video, $cover);
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

    /**
     * Get a URL to a cover image
     * 
     * @param string $url
     * 
     * @return string
     */
    private function getCover(string $url): string
    {
        $img = Http::get('https://rutube.ru/api/oembed/?url=' . rtrim($url, '/') . '/&format=json')->json('thumbnail_url');

        return $img ? strtok($img, '?') : '';
    }
}