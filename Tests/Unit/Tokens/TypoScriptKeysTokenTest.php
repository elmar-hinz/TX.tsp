<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Tokens;

use ElmarHinz\TypoScriptParser\Tokens\TypoScriptKeysToken as Token;

class TypoScriptKeysTokenTest extends \PHPUnit_Framework_TestCase
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
            '<span class="ts-objstr ts-keys">value</span>',
            $this->token->toTag());
    }

}

