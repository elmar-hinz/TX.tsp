<?php

namespace ElmarHinz\TypoScriptParser;

use \ElmarHinz\TypoScriptParser\Exceptions\TypoScriptParsetimeException;

class TypoScriptParsetimeExceptionTracker
    implements TypoScriptParsetimeExceptionTrackerPushInterface,
     TypoScriptParsetimeExceptionTrackerPullInterface
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

    /**
     * Return the exceptions of the given line.
     *
     * @see TypoScriptParsetimeExceptionTrackerPullInterface
     * @param integer $number The line number.
     */
    public function getByLineNumber($number)
    {
        if(isset($this->lineExceptions[$number])) {
            return $this->lineExceptions[$number];
        } else {
            return [];
        }
    }

    public function getFinalExceptions()
    {
        return $this->templateExceptions;
    }

}

