<?php

namespace Vhar\LaravelEmbedVideo;

readonly final class EmbedData
{
    public string $id;
    public string $video;
    public string $cover;

    /**
     * @param string $id video ID on hosting
     * 
     * @return \Vhar\LaravelEmbedVideo\EmbedData
     */
    public function setId(string $id): EmbedData
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $url URL to embedded video 
     * 
     * @return \Vhar\LaravelEmbedVideo\EmbedData
     * @throws \InvalidArgumentException
     */
    public function setVideo(string $url): EmbedData
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $this->video = $url;
        } else {
            throw new \InvalidArgumentException("The argument must be a URL string.");
        }

        return $this;
    }

    /**
     * @param string $url URL to cover image
     * 
     * @return \Vhar\LaravelEmbedVideo\EmbedData
     * @throws \InvalidArgumentException
     */
    public function setCover(string $url): EmbedData
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $this->cover = $url;
        } else {
            throw new \InvalidArgumentException("The argument must be a URL string.");
        }

        return $this;
    }
}
