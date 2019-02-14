<?php

use AnthonySan95\UrlSigner\UrlSigner;
use PHPUnit\Framework\TestCase;

class UrlSignerTest extends TestCase {

    protected $urlSigner;

    protected function setUp(): void {
        $this->urlSigner = new UrlSigner();

        $this->urlSigner->setKeyResolver(function () {
            return "random_monkey";
        });
    }

    public function testUrlSignerCreated() {
        $this->assertInstanceOf(UrlSigner::class, $this->urlSigner);
    }


    public function testTrueWhenValidatingASignedUrl() {
        $url = 'http://myapp.com/';
        $parameters = [
            'api_token' => 'randomasd'
        ];

        $signedUrl = $this->urlSigner->sign($url, $parameters);

        $this->assertTrue($this->urlSigner->validate($signedUrl));
    }


    public function testFalseWhenValidatingAForgedUrl() {
        $signedUrl = 'http://myapp.com/somewhereelse/?expires=4594900544&signature=79379e8012ebebf75a4679099477c42b16bea303e3e1cb5cb59040ab6e895f08';

        $this->assertFalse($this->urlSigner->validate($signedUrl));
    }


    public function testFalseWhenValidatingAnExpiredUrl() {
        $signedUrl = 'http://myapp.com/?expires=1123690544&signature=28a85b78db3c09bcc8194c0eff9a3db7c276371b1380296f910b77277e4f88d1';

        $this->assertFalse($this->urlSigner->validate($signedUrl));
    }


    public function testTrueWhenValidatingANonExpiredUrl() {
        $url = 'http://myapp.com';
        $expiration = 10000;

        $signedUrl = $this->urlSigner->temporarySign($url, $expiration);
        $this->assertTrue($this->urlSigner->validate($signedUrl));
    }


    public function unsignedUrlProvider() {
        return [
            ['http://myapp.com/?expires=4594900544'],
            ['http://myapp.com/?signature=79379e8012ebebf75a4679099477c42b16bea303e3e1cb5cb59040ab6e895f08'],
        ];
    }

    /**
     * @dataProvider unsignedUrlProvider
     */
    public function testFalseWhenValidatingAnUnsignedUrl($unsignedUrl) {
        $this->assertFalse($this->urlSigner->validate($unsignedUrl));
    }


    public function testKeepsQueryParametersIntact() {
        $url = 'http://myapp.com/?foo=bar&baz=qux';
        $expiration = DateTime::createFromFormat(
            'd/m/Y H:i:s',
            '10/08/2115 18:15:44',
            new DateTimeZone('Europe/Brussels')
        );
        $expectedUrl = 'http://myapp.com/?baz=qux&expires=4594900544&foo=bar&signature=2f6b9c6fd8d3a3686e4548453066740bad6393af3ff2c37cd748f4c138c7802e';

        $signedUrl = $this->urlSigner->temporarySign($url, $expiration);

        $this->assertSame($expectedUrl, $signedUrl);
        $this->assertTrue($this->urlSigner->validate($signedUrl));
    }
}
