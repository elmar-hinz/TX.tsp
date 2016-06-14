<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Tokens;

use ElmarHinz\TypoScriptParser\Tokens\AbstractTypoScriptToken as Token;

class AbstractTypoScriptTokenTest extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->token = $this->getMockBuilder(Token::class)
             ->setMethods(null)->setConstructorArgs(['value'])->getMock();
    }

    /**
     * @test
     */
    public function getDefaultValue()
    {
        $this->assertSame('value', $this->token->getValue());
    }

    /**
     * @test
     */
    public function getDefaultClasses()
    {
        $this->assertSame('ts-abstract', $this->token->getClasses());
    }

    /**
     * @test
     */
    public function getDefaultTag()
    {
        $this->assertSame('span', $this->token->getTag());
    }

    /**
     * @test
     */
    public function toDefaultTag()
    {
        $this->assertSame('<span class="ts-abstract">value</span>',
            $this->token->toTag());
    }

    /**
     * @test
     */
    public function toCustomTag()
    {
        $this->token->setTag('myTag');
        $this->token->setClasses('myAA myBB');
        $this->token->setValue('myValue');
        $this->assertSame('<myTag class="myAA myBB">myValue</myTag>',
            $this->token->toTag());
    }

}

