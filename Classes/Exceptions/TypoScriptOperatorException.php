<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

class TypoScriptOperatorException extends TypoScriptParsetimeException
{
    const CODE = 1465381315;
    const MESSAGE = 'Missing valid operator, one of "=<>{(" or ":=";';

    public function __construct($templateLineNumber)
    {
        parent::__construct($templateLineNumber);
    }
}


