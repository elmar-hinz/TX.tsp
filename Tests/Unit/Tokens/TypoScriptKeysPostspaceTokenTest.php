<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Tokens;

use \ElmarHinz\TypoScriptParser\Tokens\TypoScriptKeysPostspaceToken as Token;

class TypoScriptKeysPostspaceTokenTest extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->token = $this->getMockBuilder(Token::class)
             ->setMethods(null)->setConstructorArgs(['value'])->getMock();
    }

    /**
     * @test
     */
    public function toDefaultTag()
    {
        $this->assertSame(
            '<span class="ts-objstr_postspace ts-keys-postspace">value</span>',
            $this->token->toTag());
    }

}

