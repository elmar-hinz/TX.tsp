<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

class TypoScriptUnclosedConditionException extends TypoScriptParsetimeException
{
    const CODE = 1465385313;
    const MESSAGE = 'Open condition.';

    public function __construct()
    {
        parent::__construct(false);
    }
}


