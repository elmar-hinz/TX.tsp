<?php

namespace ElmarHinz\TypoScriptParser\Tokens;

final class TypoScriptCommentContextToken extends AbstractTypoScriptToken
{
    CONST TYPE = 'comment-context';
    protected $classes = 'ts-comment ts-comment-context';
}


