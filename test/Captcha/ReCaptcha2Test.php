<?php
namespace ZendTest\ReCaptcha2\Captcha;

use ReCaptcha2\Captcha\NoCaptchaService;
use ReCaptcha2\Captcha\ReCaptcha2;
use ReCaptcha2\Captcha\Result;
use Zend\Captcha\Exception;
use ZendTest\ReCaptcha2\Captcha\TestAsset\TestNoCaptchaService;

class ReCaptcha2Test extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->reCaptcha2 = new ReCaptcha2();
    }

    public function testConstructorShouldSetOptions()
    {
        $options = [
            'siteKey'   => 'test:siteKey',
            'secretKey' => 'test:secretKey',
            'service' => new TestNoCaptchaService,
        ];
        $recaptcha2 = new ReCaptcha2($options);

        foreach ($options as $option => $compare) {
            $getter = 'get' . ucfirst($option);
            $this->assertEquals($recaptcha2->$getter(), $compare);
        }
    }

    public function testOptionsPassedNotArrayOrTraversableWillThrowException()
    {
        $this->setExpectedException(Exception\InvalidArgumentException::class);
        $this->reCaptcha2->setOptions(new \stdClass());
    }

    public function testShouldCreateDefaultService()
    {
        $this->assertInstanceOf(NoCaptchaService::class, $this->reCaptcha2->getService());
    }

    public function testShouldAllowSpecifyingServiceObject()
    {
        $captchaService = new TestNoCaptchaService;

        $this->reCaptcha2->setService($captchaService);
        $this->assertSame($captchaService, $this->reCaptcha2->getService());
    }

    public function testShouldAllowSpecifyingServiceArray()
    {
        $ipTestValue = 'ip:test';
        $this->reCaptcha2->setService([
            'class' => TestNoCaptchaService::class,
            'options' => [
                'ip' => $ipTestValue,
            ],
        ]);

        $captchaService = $this->reCaptcha2->getService();
        $this->assertInstanceOf(TestNoCaptchaService::class, $captchaService);
        $this->assertEquals($ipTestValue, $captchaService->getIp());
    }

    public function testShouldAllowSpecifyingServiceString()
    {
        $this->reCaptcha2->setService(TestNoCaptchaService::class);
        $this->assertInstanceOf(TestNoCaptchaService::class, $this->reCaptcha2->getService());
    }

    public function testServiceClassDoesNotExistWillThrowException()
    {
        $this->setExpectedException(Exception\InvalidArgumentException::class);
        $this->reCaptcha2->setService('RandomClassThatDoesNotExist');
    }

    public function testServiceClassDoesNotImpelemtInterfaceWillThrowException()
    {
        $this->setExpectedException(Exception\DomainException::class);
        $this->reCaptcha2->setService(new \stdClass());
    }

    public function testGenerateReturnsEmptyString()
    {
        $this->assertEquals('', $this->reCaptcha2->generate());
    }

    public function testGetHelperName()
    {
        $this->assertEquals('captcha/recaptcha2', $this->reCaptcha2->getHelperName());
    }

    public function testIsValidReturnsFalseWithoutValueOrContext()
    {
        $this->assertFalse($this->reCaptcha2->isValid('', ''));

        $messages = $this->reCaptcha2->getMessages();
        $this->assertArrayHasKey(ReCaptcha2::MISSING_INPUT_RESPONSE, $messages);
        $this->assertCount(1, $messages);
    }

    public function testIsValidReturnsFalseWithoutResponseValue()
    {
        $this->assertFalse($this->reCaptcha2->isValid('foo', []));

        $messages = $this->reCaptcha2->getMessages();
        $this->assertArrayHasKey(ReCaptcha2::MISSING_INPUT_RESPONSE, $messages);
        $this->assertCount(1, $messages);
    }

    public function testIsValidReturnsFalseWhenServiceVerifyReturnsEmpty()
    {
        $serviceMock = $this->getMock(NoCaptchaService::class, ['verify'], [], '', false);
        $serviceMock->expects($this->once())
            ->method('verify')
            ->willReturn(null);

        $this->reCaptcha2->setService($serviceMock);

        $this->assertFalse($this->reCaptcha2->isValid('foo', ['g-recaptcha-response' => 'bar']));

        $messages = $this->reCaptcha2->getMessages();
        $this->assertArrayHasKey(ReCaptcha2::ERROR_CAPTCHA_GENERAL, $messages);
        $this->assertCount(1, $messages);
    }

    public function testIsValidReturnsFalseWhenServiceIsValidReturnsFalse()
    {
        $resultMock = $this->getMock(Result::class, ['isValid', 'getErrorCodes']);
        $resultMock->expects($this->once())
            ->method('isValid')
            ->willReturn(false);
        $resultMock->expects($this->once())
            ->method('getErrorCodes')
            ->willReturn(null);

        $serviceMock = $this->getMock(NoCaptchaService::class, ['verify'], [], '', false);
        $serviceMock->expects($this->once())
            ->method('verify')
            ->willReturn($resultMock);

        $this->reCaptcha2->setService($serviceMock);

        $this->assertFalse($this->reCaptcha2->isValid('foo', ['g-recaptcha-response' => 'bar']));

        $messages = $this->reCaptcha2->getMessages();
        $this->assertArrayHasKey(ReCaptcha2::ERROR_CAPTCHA_GENERAL, $messages);
        $this->assertCount(1, $messages);
    }

    public function testIsValidReturnsFalseWhenServiceIsValidReturnsFalseWithResultMessage()
    {
        $resultMock = $this->getMock(Result::class, ['isValid', 'getErrorCodes']);
        $resultMock->expects($this->once())
            ->method('isValid')
            ->willReturn(false);
        $resultMock->expects($this->once())
            ->method('getErrorCodes')
            ->willReturn(['test-error-code']);

        $serviceMock = $this->getMock(NoCaptchaService::class, ['verify'], [], '', false);
        $serviceMock->expects($this->once())
            ->method('verify')
            ->willReturn($resultMock);

        $this->reCaptcha2->setService($serviceMock);

        $this->assertFalse($this->reCaptcha2->isValid('foo', ['g-recaptcha-response' => 'bar']));

        $messages = $this->reCaptcha2->getMessages();
        $this->assertArrayHasKey(ReCaptcha2::INVALID_INPUT_RESPONSE, $messages);
        $this->assertCount(1, $messages);
    }

    public function testIsValidReturnsTrueWhenServiceIsValidReturnsTrue()
    {
        $resultMock = $this->getMock(Result::class, ['isValid']);
        $resultMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $serviceMock = $this->getMock(NoCaptchaService::class, ['verify'], [], '', false);
        $serviceMock->expects($this->once())
            ->method('verify')
            ->willReturn($resultMock);

        $this->reCaptcha2->setService($serviceMock);

        $this->assertTrue($this->reCaptcha2->isValid('foo', ['g-recaptcha-response' => 'bar']));

        $messages = $this->reCaptcha2->getMessages();
        $this->assertCount(0, $messages);
    }
}
