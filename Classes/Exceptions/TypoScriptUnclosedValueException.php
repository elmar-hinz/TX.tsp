<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

class TypoScriptUnclosedValueException extends AbstractTypoScriptParsetimeException
{
    const CODE = 1465385326;
    const MESSAGE = 'Open value.';

    public function __construct()
    {
        parent::__construct(false);
    }
}


