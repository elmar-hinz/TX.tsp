<?php

namespace ElmarHinz\TypoScriptParser\Tokens;

class TypoScriptCommentToken extends AbstractTypoScriptToken
{
    CONST TYPE = 'comment';
    protected $classes = 'ts-comment';
}


