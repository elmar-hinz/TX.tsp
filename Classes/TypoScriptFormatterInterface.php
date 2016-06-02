<?php

namespace ElmarHinz\TypoScript;

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
 * - Line number tracking
 *
 * Elements are specified by token classes. The token classes are defined as
 * constants in AbstractTypoScriptParser.
 *
 * If a number for the first line is not set, lines shall start with 1.
 */
interface TypoScriptFormatterInterface
{

	/**
	 * Set number first line.
	 *
	 * If called, it shall be called before parsing.
	 *
	 * @param integer The line number.
	 * @return void
	 */
	public function setNumberOfFirstLine($number);

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
	 * Push a token string for the current line.
	 *
	 * The token classes are defined as constants in AbstractTypoScriptParser.
	 *
	 * @param integer The token class.
	 * @param string The token string.
	 * @return void
	 */
	public function pushToken($tokenClass, $string);

	/**
	 * Push an error message fo the current line.
	 *
	 * @param string The error message.
	 * @return void
	 */
	public function pushError($message);

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
	 * @return void
	 */
	public function finishLine();

	/**
	 * Finish and return the document.
	 *
	 * Build an document of all elements, lines and errors.
	 *
	 * @return string The document.
	 */
	public function finish();
}

