<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

final class TypoScriptUnclosedCommentException
    extends AbstractTypoScriptParsetimeException
{
    const CODE = 1465385300;
    const MESSAGE = 'Open comment.';

    public function __construct()
    {
        parent::__construct(false);
    }
}


