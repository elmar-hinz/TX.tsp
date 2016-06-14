<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Tokens;

use ElmarHinz\TypoScriptParser\Tokens\TypoScriptIgnoredToken as Token;

class TypoScriptIgnoredTokenTest extends \PHPUnit_Framework_TestCase
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
            '<span class="ts-ignored">value</span>',
            $this->token->toTag());
    }

}

