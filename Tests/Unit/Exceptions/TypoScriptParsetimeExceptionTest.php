<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Exceptions;

use \ElmarHinz\TypoScriptParser\Exceptions\TypoScriptParsetimeException as Exception;

class TypoScriptParsetimeExceptionTest extends \PHPUnit_Framework_TestCase
{
    const THECLASS = '\ElmarHinz\TypoScriptParser\Exceptions\TypoScriptParsetimeException';

    public function setup()
    {
        /* $this->exception = new Exception(10); */
        /* $this->templateExecption = new Exception(false); */
        $this->exception = $this->getMockBuilder(self::THECLASS)
             ->setMethods(null)->setConstructorArgs([10])->getMock();
        $this->templateExecption = $this->getMockBuilder(self::THECLASS)
            ->setMethods(null)->setConstructorArgs([false])->getMock();
    }

    /**
     * @test
     */
    public function getMessage()
    {
        $this->assertSame(Exception::MESSAGE, $this->exception->getMessage());
        $this->assertContains('parsetime', $this->exception->getMessage());
    }

    /**
     * @test
     */
    public function getCode()
    {
        $this->assertSame(Exception::CODE, $this->exception->getCode());
    }

    /**
     * @test
     * @expectedException BadMethodCallException
     */
    public function templateExceptionsThrowExpetionForGetTemplateLineNumber()
    {
        $this->templateExecption->getTemplateLineNumber();
    }

    /**
     * @test
     */
    public function lineExceptionReturnsLineNumber()
    {
        $this->assertSame(10, $this->exception->getTemplateLineNumber());
    }

}

