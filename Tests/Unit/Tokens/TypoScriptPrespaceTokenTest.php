<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Tokens;

use ElmarHinz\TypoScriptParser\Tokens\TypoScriptPrespaceToken as Token;

class TypoScriptPrespaceTokenTest extends \PHPUnit_Framework_TestCase
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
            '<span class="ts-prespace">value</span>',
            $this->token->toTag());
    }

}

