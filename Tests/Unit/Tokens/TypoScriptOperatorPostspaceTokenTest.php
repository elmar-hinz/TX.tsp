<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Tokens;

use ElmarHinz\TypoScriptParser\Tokens\
    TypoScriptOperatorPostspaceToken as Token;

class TypoScriptOperatorPostspaceTokenTest extends \PHPUnit_Framework_TestCase
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
            '<span class="ts-operator_postspace ts-operator-postspace">' .
            'value</span>',
            $this->token->toTag()
        );
    }

}

