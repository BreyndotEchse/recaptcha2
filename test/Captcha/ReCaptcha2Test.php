<?php
namespace ZendTest\ReCaptcha2\Captcha;

use ReCaptcha2\Captcha\NoCaptchaService;
use ReCaptcha2\Captcha\ReCaptcha2;
use Zend\Captcha\Exception;
use ZendTest\ReCaptcha2\Captcha\TestAsset\TestNoCaptchaService;

class ReCaptcha2Test extends \PHPUnit_Framework_TestCase
{
    public function testConstructorShouldSetOptions()
    {
        $options = [
            'siteKey'   => 'test:siteKey',
            'secretKey' => 'test:secretKey',
            'service' => new TestNoCaptchaService,
        ];
        $recaptcha2 = new \ReCaptcha2\Captcha\ReCaptcha2($options);

        foreach ($options as $option => $compare) {
            $getter = 'get' . ucfirst($option);
            $this->assertEquals($recaptcha2->$getter(), $compare);
        }
    }

    public function testOptionsPassedNotArrayOrTraversableWillThrowException()
    {
        $recaptcha2 = new ReCaptcha2();

        $this->setExpectedException(Exception\InvalidArgumentException::class);
        $recaptcha2->setOptions(new \stdClass());
    }

    public function testShouldCreateDefaultService()
    {
        $recaptcha2 = new ReCaptcha2();

        $this->assertInstanceOf(NoCaptchaService::class, $recaptcha2->getService());
    }

    public function testShouldAllowSpecifyingServiceObject()
    {
        $recaptcha2 = new ReCaptcha2();
        $captchaService = new TestNoCaptchaService;

        $recaptcha2->setService($captchaService);
        $this->assertSame($captchaService, $recaptcha2->getService());
    }

    public function testShouldAllowSpecifyingServiceArray()
    {
        $recaptcha2 = new ReCaptcha2();

        $ipTestValue = 'ip:test';
        $recaptcha2->setService([
            'class' => TestNoCaptchaService::class,
            'options' => [
                'ip' => $ipTestValue,
            ],
        ]);

        $captchaService = $recaptcha2->getService();
        $this->assertInstanceOf(TestNoCaptchaService::class, $captchaService);
        $this->assertEquals($ipTestValue, $captchaService->getIp());
    }

    public function testShouldAllowSpecifyingServiceString()
    {
        $recaptcha2 = new ReCaptcha2();

        $recaptcha2->setService(TestNoCaptchaService::class);
        $this->assertInstanceOf(TestNoCaptchaService::class, $recaptcha2->getService());
    }

    public function testServiceClassDoesNotExistWillThrowException()
    {
        $recaptcha2 = new ReCaptcha2();

        $this->setExpectedException(Exception\InvalidArgumentException::class);
        $recaptcha2->setService('RandomClassThatDoesNotExist');
    }

    public function testServiceClassDoesNotImpelemtInterfaceWillThrowException()
    {
        $recaptcha2 = new ReCaptcha2();

        $this->setExpectedException(Exception\DomainException::class);
        $recaptcha2->setService(new \stdClass());
    }

    public function testGenerateReturnsEmptyString()
    {
        $recaptcha2 = new ReCaptcha2();

        $this->assertEquals('', $recaptcha2->generate());
    }

    public function testGetHelperName()
    {
        $recaptcha2 = new ReCaptcha2();

        $this->assertEquals('captcha/recaptcha2', $recaptcha2->getHelperName());
    }
}
