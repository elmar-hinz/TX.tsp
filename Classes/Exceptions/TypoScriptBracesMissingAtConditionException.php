<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

class TypoScriptBracesMissingAtConditionException extends TypoScriptParsetimeException
{
    const CODE = 1465381231;
    const MESSAGE = '%s closing brace(s) missing at condition.';

    public function __construct($templateLineNumber, $numberOfMissingBraces)
    {
        $this->templateLineNumberOrFalseForEndOfTemplate = $templateLineNumber;
        $message = sprintf(self::MESSAGE, $numberOfMissingBraces);
        \Exception::__construct($message, self::CODE);
    }


}

