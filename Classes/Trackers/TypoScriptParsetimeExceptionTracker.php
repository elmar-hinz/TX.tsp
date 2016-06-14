<?php

namespace ElmarHinz\TypoScriptParser\Trackers;

use ElmarHinz\TypoScriptParser\Interfaces
    \TypoScriptParsetimeExceptionInterface;

use ElmarHinz\TypoScriptParser\Interfaces
    \TypoScriptParsetimeExceptionTrackerPushInterface;

use ElmarHinz\TypoScriptParser\Interfaces
    \TypoScriptParsetimeExceptionTrackerPullInterface;

class TypoScriptParsetimeExceptionTracker
    implements TypoScriptParsetimeExceptionTrackerPushInterface,
     TypoScriptParsetimeExceptionTrackerPullInterface
{

    protected $lineExceptions = [];
    protected $templateExceptions = [];

    public function push(TypoScriptParsetimeExceptionInterface $exception)
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

