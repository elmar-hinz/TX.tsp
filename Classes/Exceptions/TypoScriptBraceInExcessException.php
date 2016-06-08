<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

class TypoScriptBraceInExcessException extends TypoScriptParsetimeException
{
    const CODE = 1465381176;
    const MESSAGE = 'A closing brace in excess.';

    public function __construct($templateLineNumber)
    {
        parent::__construct($templateLineNumber);
    }

}


