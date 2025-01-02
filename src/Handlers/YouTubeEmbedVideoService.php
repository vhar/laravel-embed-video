<?php

namespace Vhar\LaravelEmbedVideo\Handlers;

use Illuminate\Support\Facades\Http;
use Vhar\LaravelEmbedVideo\Contracts\EmbedVideoContract;
use Vhar\LaravelEmbedVideo\EmbedData;

class YouTubeEmbedVideoService implements EmbedVideoContract
{
    /**
     * YouTube url handler
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
            throw new \InvalidArgumentException("The argument is not valid URL-address to a YouTube video.");
        }

        $id = 'f';

        if ($parts['host'] === 'youtu.be') {
            $id = ltrim($parts['path'], '/');
        } elseif($parts['host'] === 'youtube.com') {
            $path = explode('/', ltrim($parts['path'], '/'));

            switch($path[0]) {
                case 'watch':
                    parse_str($parts['query'], $query);
                    $id = $query['v'] ?? 'f';
                    break;
                case 'shorts':
                case 'embed':
                    $id = $path[1] ?? 'f';
                    break;
            }
        }

        $video = 'https://www.youtube.com/embed/' . $id;
        $cover = 'https://img.youtube.com/vi/' . $id . '/0.jpg';

        return EmbedData::create($id, $video, $cover);
    }

    /**
     * Allowed YouTube domains
     * 
     * @return array
     */
    public function allowedDomains(): array
    {
        return [
            'youtu.be',
            'youtube.com',
        ];
    }
}