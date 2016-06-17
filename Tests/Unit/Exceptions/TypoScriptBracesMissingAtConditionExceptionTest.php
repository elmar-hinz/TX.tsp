<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Exceptions;

use ElmarHinz\TypoScriptParser\Exceptions\
    TypoScriptBracesMissingAtConditionException as Exception;

class TypoScriptBracesMissingAtConditionExceptionTest
    extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function getMessage1()
    {
        $exception = new Exception(10, 1);
        $expected = 'A closing brace is missing';
        $this->assertContains($expected, $exception->getMessage());
        $expected = 'condition';
        $this->assertContains($expected, $exception->getMessage());
    }

    /**
     * @test
     */
    public function getMessage3()
    {
        $exception = new Exception(10, 3);
        $expected = '3 closing braces';
        $this->assertContains($expected, $exception->getMessage());
        $expected = 'condition';
        $this->assertContains($expected, $exception->getMessage());
    }

    /**
     * @test
     */
    public function returnsLineNumber()
    {
        $exception = new Exception(10, 3);
        $this->assertSame(10, $exception->getTemplateLineNumber());
    }

}

