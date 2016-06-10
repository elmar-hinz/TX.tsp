<?php

namespace ElmarHinz\TypoScriptParser;

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

