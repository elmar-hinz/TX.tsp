<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Exceptions;

use ElmarHinz\TypoScriptParser\Exceptions\
    TypoScriptBracesMissingAtEndOfTemplateException as Exception;

class TypoScriptBracesMissingAtEndOfTemplateExceptionTest
    extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function getMessage1()
    {
        $exception = new Exception(1);
        $expected = 'A closing brace is missing.';
        $this->assertContains($expected, $exception->getMessage());
    }

    /**
     * @test
     */
    public function getMessage3()
    {
        $exception = new Exception(3);
        $expected = '3 closing braces missing.';
        $this->assertContains($expected, $exception->getMessage());
    }

    /**
     * @test
     */
    public function isEndOfTemplateException()
    {
        $exception = new Exception(4);
        $this->assertTrue($exception->isEndOfTemplateException());
    }
}

