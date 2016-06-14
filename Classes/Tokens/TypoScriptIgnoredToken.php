<?php

namespace ElmarHinz\TypoScriptParser\Tokens;

final class TypoScriptIgnoredToken extends AbstractTypoScriptToken
{
    CONST TYPE = 'ignored';
    protected $classes = 'ts-ignored';
}


