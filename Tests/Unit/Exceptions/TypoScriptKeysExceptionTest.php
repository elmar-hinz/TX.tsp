<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Exceptions;

use ElmarHinz\TypoScriptParser\Exceptions\
    TypoScriptKeysException as Exception;

class TypoScriptKeysExceptionTest extends \PHPUnit_Framework_TestCase
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
        $expected = 'alphanumeric';
        $this->assertContains($expected, $this->exception->getMessage());
        $expected = '.-_';
        $this->assertContains($expected, $this->exception->getMessage());
    }

}

