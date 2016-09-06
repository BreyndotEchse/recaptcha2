<?php
namespace ZendTest\ReCaptcha2\Captcha;

use ReCaptcha2\Captcha\NoCaptchaService;
use ZendTest\ReCaptcha2\Captcha\TestAsset\TestHttpClient;
use Zend\Http\Client\Adapter\Curl;

class NoCaptchaServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorShouldSetOptions()
    {
        $options = [
            'siteKey'   => 'test:siteKey',
            'secretKey' => 'test:secretKey',
            'ip'        => 'test:ip',
            'params'    => [
                'onload'    => 'test:onload',
                'render'    => 'test:render',
                'hl'        => 'test:hl',
            ],
        ];
        $captchaService = new NoCaptchaService($options);

        foreach ($options as $option => $compare) {
            $getter = 'get' . ucfirst($option);
            $this->assertEquals($captchaService->$getter(), $compare);
        }
    }

    public function testShouldAllowSpecifyingHttpClientObject()
    {
        $captchaService = new NoCaptchaService();
        $httpClient = new TestHttpClient;

        $captchaService->setHttpClient($httpClient);
        $this->assertSame($httpClient, $captchaService->getHttpClient());
    }

    public function testShouldAllowSpecifyingHttpClientArray()
    {
        $captchaService = new NoCaptchaService();

        $captchaService->setHttpClient([
            'class' => TestHttpClient::class,
            'options' => [
                'adapter' => Curl::class,
            ],
        ]);

        $httpClient = $captchaService->getHttpClient();
        $this->assertInstanceOf(TestHttpClient::class, $httpClient);
        $this->assertInstanceOf(Curl::class, $httpClient->getAdapter());
    }

    public function testShouldAllowSpecifyingHttpClientString()
    {
        $captchaService = new NoCaptchaService();

        $captchaService->setHttpClient(TestHttpClient::class);
        $this->assertInstanceOf(TestHttpClient::class, $captchaService->getHttpClient());
    }

    public function testGetServiceUri()
    {
        $captchaService = new NoCaptchaService();

        $this->assertEquals(NoCaptchaService::API_SERVER, $captchaService->getServiceUri());
    }
}
