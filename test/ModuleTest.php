<?php
namespace ZendTest\ReCaptcha2;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfigArray()
    {
        $module = new \ReCaptcha2\Module;

        $this->assertInternalType('array', $module->getConfig());
    }
}
