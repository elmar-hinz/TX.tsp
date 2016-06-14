<?php

namespace ElmarHinz\TypoScriptParser\Interfaces;

interface TypoScriptParserInterface
{

	/**
	 * Join multiple templates before parsing them.
	 *
	 * The template may be a multiline text
	 * or a text that is alreay split into lines.
	 *
	 * @param mixed Multiline text or array of lines.
	 */
	public function appendTemplate($template);

	/**
	 * Parse the input
	 *
	 * Depending on the type of the parser the return value may be the final
	 * TypoScript tree array, an intermediate state or something else
	 * like syntax highlighting.
	 *
	 * Depending on the type of parser this function may be called multiple
	 * times or not. If it is to be called multiple times, it will only return
	 * a copy of the intermediate state or even void for reasons of clearness.
	 * In that case it s necessary to access the internal tree by a different
	 * method.
	 *
	 * @return mixed The parsed result.
	 */
	public function parse();

}

