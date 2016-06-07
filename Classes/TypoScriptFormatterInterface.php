<?php

namespace ElmarHinz\TypoScriptParser;

/**
 * Interface of TypoScript formatters
 *
 * A formatter is called by the TypoScriptSyntax parser. Is is responsible
 * for:
 *
 * - Error tracking
 * - Error formatting
 * - Token formatting
 * - Line formatting
 * - Final document formatting
 *
 * Elements are specified by token classes. The token classes are defined as
 * constants in AbstractTypoScriptParser.
 *
 * If a number for the base line is not set, counting shall start with 1.
 */
interface TypoScriptFormatterInterface
{

	/**
     * Hide line numbers
     *
     * @return void
     */
    public function hideLineNumbers();

	/**
     * Show line numbers
     *
     * @return void
     */
    public function showLineNumbers();

	/**
	 * Set line number to count from
	 *
	 * If called, it shall be called before parsing.
	 *
	 * @param integer $number The line number.
	 * @return void
	 */
	public function setNumberOfBaseLine($number);

	/**
	 * Get the number of the last line.
	 *
	 * If lines start with 1 it is equal to the number of lines.
	 * To be called after parsing.
	 *
	 * @return integer The line number.
	 */
	public function getNumberOfLastLine();

	/**
	 * Get the number of lines.
	 *
	 * Count of all lines.
	 * To be called after parsing.
	 *
	 * @return integer The count of lines.
	 */
	public function getCountOfLines();

	/**
	 * Push a token.
	 *
	 * The token classes are defined as constants in AbstractTypoScriptParser.
     * The $lineNumber is the number within the template without offeset.
     * The first line starts with zero.
	 *
	 * @param integer $tokenClass The token class.
	 * @param integer $lineNumber the line number.
	 * @param string $string The token string.
	 * @return void
	 */
	public function pushToken($lineNumber, $tokenClass, $string);

	/**
	 * Push an error.
	 *
	 * The type and order of further arguments must matcht the $errorClass. In
	 * case there are further arguments this is documented with the error class
	 * constant in AbstractTypoScriptParser.
     *
     * The line number is the number within the template without offset.
     * The first line starts with zero.
	 *
	 * @param integer The line number.
     * @param int Error class.
	 * @param mixed Further arguments.
	 * @return void
	 */
	public function pushError();

	/**
	 * Push final error.
	 *
	 * The type and order of further arguments must matcht the $errorClass. In
	 * case there are further arguments this is documented with the error class
	 * constant in AbstractTypoScriptParser.
	 *
     * @param int Error class.
	 * @param mixed Further arguments.
	 * @return void
	 */
	public function pushFinalError();

	/**
	 * Handle the line.
	 *
	 * This may include:
	 *
	 * - Formatting elements of the line
	 * - Formatting errors of the line
	 * - Resetting the line tracking arrays
	 * - Line number counting
	 *
	 * @param integer $lineNumber the line number.
	 * @return void
	 */
	public function finishLine($lineNumber);

	/**
	 * Finish and return the document.
	 *
	 * Build an document of all elements, lines and errors.
	 *
	 * @return string The document.
	 */
	public function finish();
}

