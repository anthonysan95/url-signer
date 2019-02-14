<?php

namespace AnthonySan95\UrlSigner\Url;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use MalformedUrlException;

class Url {

    protected $parsedUrl;

    /**
     * Url constructor.
     * @param $url
     * @param array $parameters
     * @throws \Throwable
     */
    public function __construct($url, $parameters = []) {
        throw_if(empty($url), InvalidArgumentException::class);

        $this->parsedUrl = parse_url($url);

        throw_if(!is_array($this->parsedUrl), MalformedUrlException::class);

        if (isset($this->parsedUrl['query'])) {
            parse_str($this->parsedUrl['query'], $this->parsedUrl['query']);
        }

        if (isset($parameters)) {
            if (empty($this->parsedUrl['query'])) {
                $this->parsedUrl['query'] = [];
            }

            $this->parsedUrl['query'] += $parameters;
        }

        if (isset($this->parsedUrl['query'])) {
            ksort($this->parsedUrl['query']);
        }
    }

    /**
     * Get the scheme of this url or an empty string if unset.
     *
     * @return string
     */
    public function scheme() {
        return $this->parsedUrl['scheme'] ?? '';
    }

    /**
     * Get the host of this url or an empty string if unset.
     *
     * @return string
     */
    public function host() {
        return $this->parsedUrl['host'] ?? '';
    }

    /**
     * Get the port of this url or an empty string if unset.
     *
     * @return string
     */
    public function port() {
        return $this->parsedUrl['port'] ?? '';
    }

    /**
     * Get the user of this url or an empty string if unset.
     *
     * @return string
     */
    public function user() {
        return $this->parsedUrl['user'] ?? '';
    }

    /**
     * Get the pass of this url or an empty string if unset.
     *
     * @return string
     */
    public function pass() {
        return $this->parsedUrl['pass'] ?? '';
    }

    /**
     * Get the path of this url or an empty string if unset.
     *
     * @return string
     */
    public function path() {
        return $this->parsedUrl['path'] ?? '';
    }

    /**
     * Retrieve a query string item from the request.
     *
     * @param  string $key
     * @param  string|array|null $default
     * @return string|array|null
     */
    public function query($key = null, $default = null) {
        if (empty($this->parsedUrl['query'])) {
            if (!is_null($key)) {
                return $default;
            }

            return [];
        }

        if (is_null($key)) {
            return $this->parsedUrl['query'];
        }

        return $this->parsedUrl['query'][$key] ?? $default;
    }

    /**
     * Get the fragment of this url or an empty string if unset.
     *
     * @return string
     */
    public function fragment() {
        return $this->parsedUrl['fragment'] ?? '';
    }

    /**
     * Obtain a string made out of this Url.
     *
     * @param bool $withQuery
     * @return string
     */
    public function get($withQuery = true) {
        // Ex url. http://usr:pss@example.com:81/mypath/myfile.html?a=b&b[]=2&b[]=3#myfragment

        return
            ($this->scheme() ? $this->scheme() . '://' : '') .
            $this->user() .
            ($this->pass() ? ':' . $this->pass() : '') .
            (($this->user() || $this->pass()) ? '@' : '') .
            $this->host() .
            ($this->port() ? ':' . $this->port() : '') .
            $this->path() .
            ($withQuery && $this->query() ? '?' . Arr::query($this->query()) : '') .
            ($this->fragment() ? '#' . $this->fragment() : '');
    }

    /**
     * Obtain a string made out of this Url without queries.
     *
     * @return string
     */
    public function getWithoutQuery() {
        return $this->get(false);
    }

    /**
     * To string magic function, automatically called when casting to string.
     *
     * @return string
     */
    public function __toString() {
        return $this->get();
    }
}
