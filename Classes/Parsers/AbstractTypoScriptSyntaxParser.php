<?php

namespace ElmarHinz\TypoScriptParser\Parsers;

use ElmarHinz\TypoScriptParser\Interfaces\TypoScriptTokenTrackerPushInterface
    as TokenTracker;
use ElmarHinz\TypoScriptParser\Interfaces\TypoScriptPasetimeExceptionTrackerPushInterface
    as ExceptionTracker;

abstract class AbstractTypoScriptSyntaxParser extends AbstractTypoScriptParser
{
    protected $exceptionTracker = null;
    protected $tokenTracker = null;

	/**
	 * Inject the exectption tracker
	 *
     * @param ExceptionTracker $tracker The exception tracker.
	 * @return void
	 */
	public function injectExceptionTracker(ExceptionTracker $tracker)
	{
		$this->exceptionTracker = $tracker;
	}

	/**
	 * Inject the token tracker
	 *
     * @param TokenTracker $tracker The token tracker.
	 * @return void
	 */
	public function injectTokenTracker(TokenTracker $tracker)
	{
		$this->tokenTracker = $tracker;
	}

    /**
     * Push a line or finale exception to the exception tracker.
     *
     * @param mixed $lineNumberOrFalse False for final tempalte exceptions.
     * @param class $class  Full qualified or defined alias.
     */
    protected function pushException($lineNumberOrFalse, $class)
    {
        $exception = new $class($lineNumber);
        $this->exceptionTracker->push($exception);
    }

}

