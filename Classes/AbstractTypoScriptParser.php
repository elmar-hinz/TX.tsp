<?php

namespace ElmarHinz\TypoScript;

abstract class AbstractTypoScriptParser
{

	/**
	 * The lines to parse.
	 */
	protected $inputLines = Null;

	// Constants
	const DOT = '.';
	const NL = "\n";
	const EMPTY_STRING = '';

	// Matchers
	const COMMENT_CONTEXT_CLOSE_REGEX = '|^(\s*)(\*/)(.*)$|';
	const COMMENT_CONTEXT_OPEN_REGEX = '|^(\s*)(/\*)(.*)$|';
	const COMMENT_REGEX = '/^(\s*)(#|\/[^\*])(.*)$/';
	const CONDITION_REGEX = '|^(\s*)(\[.*)$|';
	const LEVEL_CLOSE_REGEX = '|^(\s*)(})(.*)$|';
	const OPERATOR_REGEX = '/^(\s*)([[:alnum:].\\\\_-]*[[:alnum:]\\\\_-])(\s*)(:=|[=<>{(])(\s*)(.*)$/';
	const VALUE_CONTEXT_CLOSE_REGEX = '|^(\s*)(\))(.*)$|';
	const VOID_REGEX = '|^(\s*)$|';

	// Operators
	const ASSIGN_OPERATOR = '=';
	const COPY_OPERATOR = '<';
	const LEVEL_OPEN_OPERATOR = '{';
	const MODIFY_OPERATOR = ':=';
	const UNSET_OPERATOR = '>';
	const VALUE_CONTEXT_OPEN_OPERATOR = '(';

	// Contexts
	const COMMENT_CONTEXT = 1;
	const DEFAULT_CONTEXT = 2;
	const VALUE_CONTEXT = 3;

	/**
	 * Join multiple templates before parsing them.
	 *
	 * The template may be a multiline text
	 * or a text that is alreay split into lines.
	 *
	 * @param mixed Multiline text or array of lines.
	 */
	public function appendTemplate($template)
	{
		if (!is_array($template)) {
			if(substr($template, -1) == "\n")
			   $template = substr($template, 0, -1);
			$template = explode("\n", $template);
		}
		if($this->inputLines == Null) {
			$this->inputLines = $template;
		} else {
			foreach ($template as $line)  $this->inputLines[] = $line;
		}
	}

	/**
	 * Parse the input
	 *
	 * Depending on the type of the parser the return value may be the final
	 * TypoScript tree array or an intermediate state.
	 *
	 * @return array The parsed result.
	 */
	public abstract function parse();

}

