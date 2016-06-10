<?php

namespace ElmarHinz\TypoScriptParser;

use ElmarHinz\TypoScriptParser\TypoScriptTokenTrackerPushInterface
    as TokenTracker;
use ElmarHinz\TypoScriptParser\TypoScriptPasetimeExceptionTrackerPushInterface
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

}

