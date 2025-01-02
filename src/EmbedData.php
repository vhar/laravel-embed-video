<?php

namespace Vhar\LaravelEmbedVideo;

readonly final class EmbedData
{
    /**
     * @param string $id
     * @param string $video
     * @param string $cover
     */
    private function __construct(
        public string $id,
        public string $video,
        public string $cover,
    ) 
    {
        //
    }

    /**
     * @param string $id video ID on hosting
     * @param string $video URL to embedded video 
     * @param string $cover URL to cover image
     * 
     * @return \Vhar\LaravelEmbedVideo\EmbedData
     */
    public static function create(
        string $id,
        string $video,
        string $cover,
    ): EmbedData
    {
        return new EmbedData(
            $id,
            $video,
            $cover,
        );
    }
}
