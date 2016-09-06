<?php
namespace ZendTest\ReCaptcha2\Captcha;

use ReCaptcha2\Captcha\NoCaptchaService;
use ReCaptcha2\Captcha\ReCaptcha2;
use ZendTest\ReCaptcha2\Captcha\TestAsset\TestNoCaptchaService;

class ReCaptcha2Test extends \PHPUnit_Framework_TestCase
{
    public function testConstructorShouldSetOptions()
    {
        $options = [
            'siteKey'   => 'test:siteKey',
            'secretKey' => 'test:secretKey',
        ];
        $recaptcha2 = new \ReCaptcha2\Captcha\ReCaptcha2($options);

        foreach ($options as $option => $compare) {
            $getter = 'get' . ucfirst($option);
            $this->assertEquals($recaptcha2->$getter(), $compare);
        }
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
}
