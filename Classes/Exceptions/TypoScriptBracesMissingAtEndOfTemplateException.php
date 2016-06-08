<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

class TypoScriptBracesMissingAtEndOfTemplateException extends TypoScriptParsetimeException
{
    const CODE = 1465381270;
    const MESSAGE = '%s closing brace(s) missing.';

    public function __construct($numberOfMissingBraces)
    {
        $this->templateLineNumberOrFalseForEndOfTemplate = false;
        $message = sprintf(self::MESSAGE, $numberOfMissingBraces);
        \Exception::__construct($message, self::CODE);
    }

}
