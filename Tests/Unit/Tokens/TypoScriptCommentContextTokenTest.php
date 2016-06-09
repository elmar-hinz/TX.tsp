<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Tokens;

use \ElmarHinz\TypoScriptParser\Tokens\TypoScriptCommentContextToken as Token;

class TypoScriptCommentContextTokenTest extends \PHPUnit_Framework_TestCase
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
            '<span class="ts-comment ts-comment-context">value</span>',
            $this->token->toTag());
    }

}

