<?php

namespace ElmarHinz\TypoScriptParser\Tokens;

class TypoScriptIgnoredToken extends AbstractTypoScriptToken
{
    CONST TYPE = 'ignored';
    protected $classes = 'ts-ignored';
}


