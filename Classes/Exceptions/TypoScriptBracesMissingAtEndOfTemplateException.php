<?php

namespace ElmarHinz\TypoScriptParser\Exceptions;

final class TypoScriptBracesMissingAtEndOfTemplateException
    extends AbstractTypoScriptParsetimeException
{
    const CODE = 1465381270;
    const MESSAGE1 = 'A closing brace is missing.';
    const MESSAGE2 = '%s closing braces missing.';

    public function __construct($numberOfMissingBraces)
    {
        $this->templateLineNumberOrFalseForEndOfTemplate = false;
        if($numberOfMissingBraces == 1)
            $message = self::MESSAGE1;
        else
            $message = sprintf(self::MESSAGE2, $numberOfMissingBraces);
        \Exception::__construct($message, self::CODE);
    }

}

