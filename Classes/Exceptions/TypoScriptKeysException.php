<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

final class TypoScriptKeysException extends AbstractTypoScriptParsetimeException
{
    const CODE = 1465381296;
    const MESSAGE = 'Missing valid keys, limited to alphanumeric and ".-_\\".';

    public function __construct($templateLineNumber)
    {
        parent::__construct($templateLineNumber);
    }
}


