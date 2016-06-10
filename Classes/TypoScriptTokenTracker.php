<?php

namespace ElmarHinz\TypoScriptParser;

use \ElmarHinz\TypoScriptParser\Tokens\AbstractTypoScriptToken;

/**
 * Tracker of the tokens while parsing a TypoScript Template.
 *
 * Usage
 *
 * Parsing:
 *
 * $tracker = new TypoScriptTokenTracker();
 * $tracker->push($token); // multiple times per line
 * $tracker->nextLine();   // start with the next line
 * ... for all lines
 *
 * Reading:
 *
 * foreach($tracker as $lineNumber => $tokens) {
 *  ... do stuff
 * }
 *
 * The internal line pointer starts with 1 for the first line.
 */
class TypoScriptTokenTracker implements \Iterator
{

    protected $tokens = [];
    protected $line = 1;

	/**
	 * Push a token for the current line.
	 *
	 * @param AbstractTypoScriptToken $token The token to push.
	 * @return void
	 */
	public function push(AbstractTypoScriptToken $token)
	{
        $this->tokens[$this->line][] = $token;
	}

    /**
     * Increase the current line number.
     *
     * The method is to be called at the end of each line. The line number, by
     * which the tokens are organized, is increased.
     *
     * Don't call it before reading the first line.
     * It doesn't matter to call it after the last line.
     *
     * Alias for next().
     *
	 * @return void
     */
    public function nextLine()
    {
        $this->line++;
    }

    /**
     * Get the tokens for the given line number.
     *
     * @param integer $line The line number.
     * @return array The tokens of the given line or empty array.
     */
    public function getByLine($line)
    {
        if(isset($this->tokens[$line])) {
            return $this->tokens[$line];
        } else {
            return [];
        }
    }

    /**
     * Get the count of lines
     *
     * The count of lines is equal to the number of the last line,
     * as counting starts with 1.
     *
     * @return integer The count of lines.
     */
    public function getCountOfLines()
    {
        return count($this->tokens);
    }

    /**********************************************************************
    * Iterator interface
    **********************************************************************/

    /**
     * Rewind the lines
     *
     * @return void
     */
    public function rewind() {
        $this->line = 1;
    }

    /**
     * Get the tokens of the current line.
     *
     * @see \Iterator
     * @return array The tokens of the current line.
     */
    public function current() {
        return $this->tokens[$this->line];
    }

    /**
     * Get the current line number
     *
     * @see \Iterator
     * @return integer The current line number.
     */
    public function key() {
        return $this->line;
    }

    /**
     * Increase the current line number.
     *
     * @see \Iterator
     * @return void
     */
    public function next() {
        $this->line++;
    }

    /**
     * Check if the line pointer is still within the range of lines.
     *
     * @see \Iterator
     * @return boolean True, while inside the range of lines.
     */
    public function valid() {
        return isset($this->tokens[$this->line]);
    }

}

