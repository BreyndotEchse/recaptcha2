<?php
namespace ZendTest\ReCaptcha2\View\Helper\Captcha;

use ReCaptcha2\Captcha\NoCaptchaService;
use ReCaptcha2\Captcha\ReCaptcha2;
use ReCaptcha2\Form\View\Helper\Captcha\ReCaptcha2 as ReCaptcha2ViewHelper;
use Zend\Form\Element\Captcha;
use Zend\Form\Exception;

class ReCaptcha2Test extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->helper = new ReCaptcha2ViewHelper;
        parent::setUp();
    }

    public function testInvokeReturnsSelfWithoutElementParameter()
    {
        $helper = $this->helper;
        $this->assertSame($this->helper, $helper());
    }

    public function testRenderWithoutCaptchaServiceWillThrowException()
    {
        $captchaMock = $this->getMock(Captcha::class, ['getCaptcha']);
        $captchaMock->expects($this->once())
            ->method('getCaptcha')
            ->willReturn(null);

        $this->setExpectedException(Exception\DomainException::class, 'requires that the element has a "captcha" attribute implementing');
        $this->helper->render($captchaMock);
    }

    public function testRenderWithoutSiteKeyServiceWillThrowException()
    {
        $captchaMock = $this->getMock(Captcha::class, ['getCaptcha']);
        $captchaMock->expects($this->once())
            ->method('getCaptcha')
            ->willReturn(new ReCaptcha2);

        $this->setExpectedException(Exception\DomainException::class, 'Missing site key');
        $this->helper->render($captchaMock);
    }

    public function testRender()
    {
        $noCaptchaService = new NoCaptchaService([
            'siteKey' => 'test-site-key',
            'params' => [
                'render' => 'test-param-render',
            ],
        ]);
        $reCaptcha2Mock = $this->getMock(ReCaptcha2::class, ['getService']);
        $reCaptcha2Mock->expects($this->once())
            ->method('getService')
            ->willReturn($noCaptchaService);

        $captchaMock = $this->getMock(Captcha::class, ['getCaptcha', 'getAttributes', 'getName']);
        $captchaMock->expects($this->once())
            ->method('getCaptcha')
            ->willReturn($reCaptcha2Mock);
        $captchaMock->expects($this->once())
            ->method('getAttributes')
            ->willReturn(['data-theme' => 'dark']);
        $captchaMock->expects($this->once())
            ->method('getName')
            ->willReturn('test-name');

        $helper = $this->helper;
        $result = $helper($captchaMock);
        $this->assertContains('<input type="hidden" name="test-name"', $result);
        $this->assertContains('<div data-theme="dark" class="g-recaptcha" data-sitekey="test-site-key"', $result);
        $this->assertContains(sprintf('<iframe src="%s/fallback?render=test-param-render&amp;k=test-site-key"', NoCaptchaService::API_SERVER), $result);
    }
}
