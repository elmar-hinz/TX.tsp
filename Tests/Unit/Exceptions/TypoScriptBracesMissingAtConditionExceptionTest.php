<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Exceptions;

use ElmarHinz\TypoScriptParser\Exceptions\
    TypoScriptBracesMissingAtConditionException as Exception;

class TypoScriptBracesMissingAtConditionExceptionTest
    extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->exception = new Exception(10, 3);
    }

    /**
     * @test
     */
    public function getMessage()
    {
        $expected = '3 closing brace(s)';
        $this->assertContains($expected, $this->exception->getMessage());
        $expected = 'condition';
        $this->assertContains($expected, $this->exception->getMessage());
    }

    /**
     * @test
     */
    public function returnsLineNumber()
    {
        $this->assertSame(10, $this->exception->getTemplateLineNumber());
    }

}

