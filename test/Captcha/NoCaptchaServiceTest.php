<?php
namespace ZendTest\ReCaptcha2\Captcha;

use ReCaptcha2\Captcha\NoCaptchaService;
use Zend\Captcha\Exception;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;
use ZendTest\ReCaptcha2\Captcha\TestAsset\TestHttpClient;

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

    public function testShouldCreateDefaultHttpClient()
    {
        $captchaService = new NoCaptchaService();

        $this->assertInstanceOf(Client::class, $captchaService->getHttpClient());
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

    public function testHttpClientClassDoesNotExistWillThrowException()
    {
        $captchaService = new NoCaptchaService();

        $this->setExpectedException(Exception\InvalidArgumentException::class);
        $captchaService->setHttpClient('RandomClassThatDoesNotExist');
    }

    public function testHttpClientClassDoesNotImpelemtInterfaceWillThrowException()
    {
        $captchaService = new NoCaptchaService();

        $this->setExpectedException(Exception\DomainException::class);
        $captchaService->setHttpClient(new \stdClass());
    }

    public function testGetServiceUri()
    {
        $captchaService = new NoCaptchaService();

        $this->assertEquals(NoCaptchaService::API_SERVER, $captchaService->getServiceUri());
    }
}
