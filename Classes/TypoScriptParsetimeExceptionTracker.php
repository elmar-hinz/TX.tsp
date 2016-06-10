<?php

namespace ElmarHinz\TypoScriptParser;

use \ElmarHinz\TypoScriptParser\Exceptions\TypoScriptParsetimeException;

class TypoScriptParsetimeExceptionTracker
{

    protected $lineExceptions = [];
    protected $templateExceptions = [];

    public function push(TypoScriptParsetimeException $exception)
    {
        if($exception->isEndOfTemplateException()) {
            $this->templateExceptions[] = $exception;
        } else {
            $this->lineExceptions[$exception->getTemplateLineNumber()][]
                = $exception;
        }
    }

    public function getByLine($lineNumber)
    {
        if(isset($this->lineExceptions[$lineNumber])) {
            return $this->lineExceptions[$lineNumber];
        } else {
            return [];
        }
    }

    public function getFinalExceptions()
    {
        return $this->templateExceptions;
    }

}

