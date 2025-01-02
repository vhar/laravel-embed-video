<?php

namespace Vhar\LaravelEmbedVideo;

use Illuminate\Container\Container;


class EmbedVideoService
{
    /**
     * The current video hosting class-handler service.
     * 
     * @var \Vhar\LaravelEmbedVideo\Contracts\EmbedVideoContract
     */
    protected $hosting;

    /**
     * Set video hosting handler class
     * 
     * @param string $alias
     * 
     * @return \Vhar\LaravelEmbedVideo\Contracts\EmbedVideoContract|null
     * 
     * @throws \InvalidArgumentException
     */
    public function hosting(string $alias)
    {
        $handler = Container::getInstance()->make('videohosting')->getClassHostingAlias($alias);

        if (is_null($handler)) {
            throw new \InvalidArgumentException("Class for {$alias} is not defined");
        }

        return $this->hosting = new $handler;
    }

    /**
     * @param string $methodEmbedVideoContract
     * @param array $args
     * 
     * @return \Vhar\LaravelEmbedVideo\Contracts\EmbedVideoContract
     * 
     * @throws \InvalidArgumentException
     */
    public function __call(string $method, array $args)
    {   
        $domain = parse_url($args[0], PHP_URL_HOST);

        if (is_null($domain)) {
            throw new \InvalidArgumentException("The argument must be a URL.");
        }

        $alias = $this->getAlias($domain);

        if (is_null($alias)) {
            throw new \InvalidArgumentException("There are no registered handlers for the domain {$domain}");
        }

        return $this->hosting($alias)->{$method}(...$args);
    }

    /**
     * Get the registered video hosting handler class by hosting domain from URL
     * 
     * @param string $domain
     * 
     * @return string|null
     */
    private function getAlias(string $domain)
    {
        $handlers = Container::getInstance()->make('videohosting')->getClassHostingAliases();

        $domain = str_replace('www.', '', $domain);

        foreach ($handlers as $alias => $handler) {
            $hosting = new $handler;

            if (in_array($domain , $hosting->allowedDomains())) {
                return $alias;
            }
        }

        return null;
    }
}