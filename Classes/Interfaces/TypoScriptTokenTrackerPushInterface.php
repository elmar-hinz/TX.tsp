<?php

namespace ElmarHinz\TypoScriptParser\Interfaces;

/**
 * Tracker of the tokens while parsing a TypoScript Template.
 *
 * Usage
 *
 * $tracker = new TypoScriptTokenTracker();
 * $tracker->push($token); // multiple times per line
 * $tracker->nextLine();   // start with the next line
 * ... for all lines
 *
 */
interface TypoScriptTokenTrackerPushInterface
{

	/**
	 * Push a token for the current line.
	 *
	 * @param TypoScriptTokenInterface $token The token to push.
	 * @return void
	 */
	public function push(TypoScriptTokenInterface $token);

    /**
     * Increase the current line number.
     *
     * The method is to be called at the end of each line. The line number, by
     * which the tokens are organized, is increased.
     *
     * Don't call it before reading the first line.
     * It doesn't matter to call it after the last line.
     *
	 * @return void
     */
    public function nextLine();

}

