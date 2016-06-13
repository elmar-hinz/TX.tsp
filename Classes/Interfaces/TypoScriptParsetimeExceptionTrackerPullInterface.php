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
interface TypoScriptParsetimeExceptionTrackerPullInterface
{

    /**
     * Return the exceptions of the given line.
     *
     * @param integer $number The line number.
     * @return array List of exceptions.
     */
    public function getByLineNumber($number);

    /**
     * Return the exceptions detected at the end of the template.
     *
     * @return array List of exceptions.
     */
    public function getFinalExceptions();

}

