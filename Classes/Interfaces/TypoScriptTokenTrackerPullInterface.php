<?php

namespace ElmarHinz\TypoScriptParser\Interfaces;

/**
 * Pull the tokens from parsing a TypoScript template.
 *
 * Usage
 *
 * foreach($tracker as $lineNumber => $tokens) {
 *  ... do stuff
 * }
 *
 */
interface TypoScriptTokenTrackerPullInterface extends \Iterator
{

}

