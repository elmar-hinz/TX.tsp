<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Exceptions;

use \ElmarHinz\TypoScriptParser\Exceptions\TypoScriptUnclosedValueException as Exception;

class TypoScriptUnclosedValueExceptionTest extends \PHPUnit_Framework_TestCase
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
        $expected = 'Open value.';
        $this->assertContains($expected, $this->exception->getMessage());
    }

    /**
     * @test
     */
    public function isEndOfTemplateException()
    {
        $this->assertTrue($this->exception->isEndOfTemplateException());
    }
}

