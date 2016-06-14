<?php

namespace ElmarHinz\TypoScriptParser\Interfaces;

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
    public function push(TypoScriptParsetimeExceptionInterface $exception);

}

