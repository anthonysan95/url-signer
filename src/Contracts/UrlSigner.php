<?php

namespace AnthonySan95\UrlSigner\Contracts;

interface UrlSigner {

    /**
     * Create a signed URL.
     *
     * @param  string $url
     * @param  array $parameters
     * @param  \DateTimeInterface|\DateInterval|int $expiration
     * @return string
     */
    public function sign($url, $parameters = [], $expiration = null): string;

    /**
     * Create a temporary signed URL.
     *
     * @param  string  $url
     * @param  \DateTimeInterface|\DateInterval|int  $expiration
     * @param  array  $parameters
     * @return string
     */
    public function temporarySign($url, $expiration, $parameters = []): string;

    /**
     * Validate a signed url.
     *
     * @param string $url
     *
     * @return bool
     */
    public function validate($url);

}
