<?php

namespace ElmarHinz\TypoScriptParser;

use \ElmarHinz\TypoScriptParser\Exceptions\TypoScriptParsetimeException;

/**
 * Tracker of the exceptions while parsing a TypoScript Template.
 *
 * Usage
 *
 * $tracker = new TypoScriptParsetimeExceptionTracker();
 * $tracker->push($exception);
 * ... for all exceptons
 *
 * The line number is contained within the exceptions.
 */
interface TypoScriptParsetimeExceptionTrackerPushInterface
{

    /**
     * Push an excption.
     *
     * @param TypoScriptParsetimeException $exception The exception.
     * @return void
     */
    public function push(TypoScriptParsetimeException $exception);

}

