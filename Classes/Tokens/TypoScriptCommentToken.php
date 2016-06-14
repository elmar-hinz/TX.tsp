<?php

namespace ElmarHinz\TypoScriptParser\Tokens;

final class TypoScriptCommentToken extends AbstractTypoScriptToken
{
    CONST TYPE = 'comment';
    protected $classes = 'ts-comment';
}


