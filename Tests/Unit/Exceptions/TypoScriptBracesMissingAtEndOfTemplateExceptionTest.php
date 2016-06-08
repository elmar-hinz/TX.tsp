<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Exceptions;

use \ElmarHinz\TypoScriptParser\Exceptions\TypoScriptBracesMissingAtEndOfTemplateException as Exception;

class TypoScriptBracesMissingAtEndOfTemplateExceptionTest
    extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->exception = new Exception(4);
    }

    /**
     * @test
     */
    public function getMessage()
    {
        $expected = '4 closing brace(s)';
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

