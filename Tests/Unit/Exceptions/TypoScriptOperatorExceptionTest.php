<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Exceptions;

use \ElmarHinz\TypoScriptParser\Exceptions\TypoScriptOperatorException as Exception;

class TypoScriptOperatorExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->exception = new Exception(10);
    }

    /**
     * @test
     */
    public function getMessage()
    {
        $expected = '=<>{(';
        $this->assertContains($expected, $this->exception->getMessage());
        $expected = ':=';
        $this->assertContains($expected, $this->exception->getMessage());
    }

    /**
     * @test
     */
    public function getCode()
    {
        $expect = 1465381315;
        $this->assertSame($expect,  $this->exception->getCode());
        $this->assertSame(Exception::CODE, $this->exception->getCode());
    }

}

