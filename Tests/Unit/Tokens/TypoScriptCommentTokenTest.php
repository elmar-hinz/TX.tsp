<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Tokens;

use \ElmarHinz\TypoScriptParser\Tokens\TypoScriptCommentToken as Token;

class TypoScriptCommentTokenTest extends \PHPUnit_Framework_TestCase
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
            '<span class="ts-comment">value</span>',
            $this->token->toTag());
    }

}

