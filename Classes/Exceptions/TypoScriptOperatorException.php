<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

final class TypoScriptOperatorException extends AbstractTypoScriptParsetimeException
{
    const CODE = 1465381315;
    const MESSAGE = 'Missing valid operator, one of "=<>{(" or ":=";';

    public function __construct($templateLineNumber)
    {
        parent::__construct($templateLineNumber);
    }
}


