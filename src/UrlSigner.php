<?php

namespace AnthonySan95\UrlSigner;

use AnthonySan95\UrlSigner\Contracts\UrlSigner as UrlSignerContract;
use AnthonySan95\UrlSigner\Url\Url;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\InteractsWithTime;


class UrlSigner implements UrlSignerContract {
    use InteractsWithTime;

    /**
     * The encryption key resolver callable.
     *
     * @var callable
     */
    protected $keyResolver;

    /**
     * Create a signed URL.
     *
     * @param  string $url
     * @param  array $parameters
     * @param  \DateTimeInterface|\DateInterval|int $expiration
     * @return string
     * @throws \Throwable
     */
    public function sign($url, $parameters = [], $expiration = null): string {
        $parameters = Arr::wrap($parameters);

        if ($expiration) {
            $parameters = $parameters + ['expires' => $this->availableAt($expiration)];
        }

        ksort($parameters);

        $url = new Url($url, $parameters);

        $key = call_user_func($this->keyResolver);

        $url = new Url($url, $parameters + [
                'signature' => hash_hmac('sha256', $url, $key),
            ]);

        return $url;
    }

    /**
     * Create a temporary signed URL.
     *
     * @param  string $url
     * @param  \DateTimeInterface|\DateInterval|int $expiration
     * @param  array $parameters
     * @return string
     * @throws \Throwable
     */
    public function temporarySign($url, $expiration, $parameters = []): string {
        return $this->sign($url, $parameters, $expiration);
    }

    /**
     * Validate a signed url.
     *
     * @param string $url
     *
     * @return bool
     * @throws \Throwable
     */
    public function validate($url) {
        $url = new Url($url);

        $original = rtrim($url->getWithoutQuery() . '?' . Arr::query(
                Arr::except($url->query(), 'signature')
            ), '?');

        $expires = $url->query('expires');

        $signature = hash_hmac('sha256', $original, call_user_func($this->keyResolver));

        return hash_equals($signature, $url->query('signature', '')) &&
            !($expires && Carbon::now()->getTimestamp() > $expires);
    }


    /**
     * Set the encryption key resolver.
     *
     * @param  callable $keyResolver
     * @return $this
     */
    public function setKeyResolver(callable $keyResolver) {
        $this->keyResolver = $keyResolver;

        return $this;
    }
}
