<?php
namespace ZendTest\ReCaptcha2\Captcha;

use ReCaptcha2\Captcha\NoCaptchaService;
use ReCaptcha2\Captcha\Result;
use Zend\Captcha\Exception;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;
use ZendTest\ReCaptcha2\Captcha\TestAsset\TestHttpClient;

class NoCaptchaServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->captchaService = new NoCaptchaService;
    }

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

    public function testOptionsPassedNotArrayOrTraversableWillThrowException()
    {
        $this->setExpectedException(Exception\InvalidArgumentException::class);
        $this->captchaService->setOptions(new \stdClass());
    }

    public function testShouldCreateDefaultHttpClient()
    {
        $this->assertInstanceOf(Client::class, $this->captchaService->getHttpClient());
    }

    public function testShouldAllowSpecifyingHttpClientObject()
    {
        $httpClient = new TestHttpClient;

        $this->captchaService->setHttpClient($httpClient);
        $this->assertSame($httpClient, $this->captchaService->getHttpClient());
    }

    public function testShouldAllowSpecifyingHttpClientArray()
    {
        $this->captchaService->setHttpClient([
            'class' => TestHttpClient::class,
            'options' => [
                'adapter' => Curl::class,
            ],
        ]);

        $httpClient = $this->captchaService->getHttpClient();
        $this->assertInstanceOf(TestHttpClient::class, $httpClient);
        $this->assertInstanceOf(Curl::class, $httpClient->getAdapter());
    }

    public function testShouldAllowSpecifyingHttpClientString()
    {
        $this->captchaService->setHttpClient(TestHttpClient::class);
        $this->assertInstanceOf(TestHttpClient::class, $this->captchaService->getHttpClient());
    }

    public function testHttpClientClassDoesNotExistWillThrowException()
    {
        $this->setExpectedException(Exception\InvalidArgumentException::class);
        $this->captchaService->setHttpClient('RandomClassThatDoesNotExist');
    }

    public function testHttpClientClassDoesNotImpelementInterfaceWillThrowException()
    {
        $this->setExpectedException(Exception\DomainException::class);
        $this->captchaService->setHttpClient(new \stdClass());
    }

    public function testGetServiceUri()
    {
        $this->assertEquals(NoCaptchaService::API_SERVER, $this->captchaService->getServiceUri());
    }

    public function testVerifyWithoutSecretKeyWillThrowException()
    {
        $this->setExpectedException(Exception\DomainException::class);
        $this->captchaService->verify('foo');
    }

    public function testVerify()
    {
        $httpClientMock = $this->getMock(Client::class, ['send'], [], '', false);
        $httpClientMock->expects($this->once())
            ->method('send')
            ->willReturn(new \Zend\Http\Response);

        $this->captchaService->setIp('test');
        $this->captchaService->setSecretKey('secret-key');
        $this->captchaService->setHttpClient($httpClientMock);

        $this->assertInstanceOf(Result::class, $this->captchaService->verify('foo'));
    }
}
