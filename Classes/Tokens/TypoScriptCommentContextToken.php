<?php

namespace ElmarHinz\TypoScriptParser\Tokens;

class TypoScriptCommentContextToken extends AbstractTypoScriptToken
{
    CONST TYPE = 'comment-context';
    protected $classes = 'ts-comment ts-comment-context';
}


