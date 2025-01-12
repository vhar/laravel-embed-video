<?php

namespace Vhar\LaravelEmbedVideo\Handlers;

use Illuminate\Support\Facades\Http;
use Vhar\LaravelEmbedVideo\Contracts\EmbedVideoContract;
use Vhar\LaravelEmbedVideo\EmbedData;

class VKVideoEmbedVideoService implements EmbedVideoContract
{
    /**
     * VK Video url handler
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

        Http::get($url)->throwUnlessStatus(200);

        $parts = parse_url($url);
        $parts['host'] = str_replace('www.', '', $parts['host']);

        if (!in_array($parts['host'], $this->allowedDomains())) {
            throw new \InvalidArgumentException("The argument is not valid URL-address to a VK Video.");
        }

        if (!empty($parts['query'])) {
            parse_str($parts['query'], $path);
        }

        preg_match('/[video|clip](-?[0-9]+)[_]([0-9]+)[_]?([0-9a-f]+)?/', ($path['z'] ?? $parts['path']), $part);

        if (!empty($part[1]) && !empty($part[2])) {
            $path = [
                'oid' => $part[1],
                'id'  => $part[2],
                'hash' => $part[3] ?? null
            ];
        }

        $id =  $path['oid'] . '_' . $path['id'];
        $video = 'https://vk.com/video_ext.php?oid=' . $path['oid'] . '&id=' . $path['id'];

        if (!empty($path['hash'])) {
            $id .= '_' . $path['hash'];
            $video .= '&hash=' . $path['hash'];
        }

        $cover = $this->getCover($video);

        $data = new EmbedData();

        return $data->setId($id)->setVideo($video)->setCover($cover);
    }

    /**
     * Allowed VKVideo domains
     * 
     * @return array
     */
    public function allowedDomains(): array
    {
        return [
            'vk.com',
            'vk.ru',
            'vkvideo.ru',
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
        $img = '';

        $body = Http::withHeaders([
            'Host' => parse_url($url, PHP_URL_HOST)
        ])
            ->get($url)
            ->body();

        $doc = new \DOMDocument();
        @$doc->loadHTML($body);

        $xpath = new \DOMXpath($doc);

        $node = $xpath->query('//*[contains(@class, "video_box_msg_background")]');

        if ($node[0]) {
            $style = $node[0]->getAttribute('style');

            preg_match("/background-image:url\((.*)\)/", $style, $matches);

            $img = $matches[1] ?? '';
        }

        return $img;
    }
}
