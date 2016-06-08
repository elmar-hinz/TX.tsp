<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Exceptions;

use \ElmarHinz\TypoScriptParser\Exceptions\TypoScriptParsetimeException as Exception;

class TypoScriptParsetimeExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->exception = $this->getMockBuilder(Exception::class)
             ->setMethods(null)->setConstructorArgs([10])->getMock();
        $this->templateExecption = $this->getMockBuilder(Exception::class)
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

