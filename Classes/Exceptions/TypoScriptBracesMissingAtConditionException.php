<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

final class TypoScriptBracesMissingAtConditionException
    extends AbstractTypoScriptParsetimeException
{
    const CODE = 1465381231;
    const MESSAGE1 = 'A closing brace is missing at condition.';
    const MESSAGE2 = '%s closing braces missing at condition.';

    public function __construct($templateLineNumber, $numberOfMissingBraces)
    {
        $this->templateLineNumberOrFalseForEndOfTemplate = $templateLineNumber;
        if($numberOfMissingBraces == 1)
            $message = self::MESSAGE1;
        else
            $message = sprintf(self::MESSAGE2, $numberOfMissingBraces);
        \Exception::__construct($message, self::CODE);
    }


}

