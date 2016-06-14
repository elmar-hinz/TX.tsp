<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Tokens;

use ElmarHinz\TypoScriptParser\Tokens\TypoScriptOperatorToken as Token;

class TypoScriptOperatorTokenTest extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->token = new Token('value');
    }

    /**
     * @test
     */
    public function toDefaultTag()
    {
        $this->assertSame(
            '<span class="ts-operator">value</span>',
            $this->token->toTag());
    }

}

