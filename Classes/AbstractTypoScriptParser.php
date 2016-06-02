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
	const COMMENT_CONTEXT_CLOSE_REGEX = '|^(\s*)(\*/)(\s*)(.*)$|';
	const COMMENT_CONTEXT_OPEN_REGEX = '|^(\s*)(/\*)(.*)$|';
	const COMMENT_REGEX = '/^(\s*)(#|\/[^\*])(.*)$/';
	const CONDITION_REGEX = '|^(\s*)(\[.*)$|';
	const LEVEL_CLOSE_REGEX = '|^(\s*)(})(.*)$|';
	const OPERATOR_REGEX = '/^(\s*)([[:alnum:].\\\\_-]*[[:alnum:]\\\\_-])(\s*)(:=|[=<>{(])(\s*)(.*)$/';
	const VALUE_CONTEXT_CLOSE_REGEX = '|^(\s*)(\))(\s*)(.*)$|';
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

	// Syntax token classes
	const COMMENT_CONTEXT_TOKEN    = 1;
	const COMMENT_TOKEN            = 2;
	const CONDITION_TOKEN          = 3;
	const IGNORED_TOKEN            = 4;
	const KEYS_POSTSPACE_TOKEN     = 5;
	const KEYS_TOKEN               = 6;
	const OPERATOR_POSTSPACE_TOKEN = 7;
	const OPERATOR_TOKEN           = 8;
	const PRESPACE_TOKEN           = 9;
	const VALUE_CONTEXT_TOKEN      = 10;
	const VALUE_COPY_TOKEN         = 11;
	const VALUE_TOKEN              = 12;

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
	 * Inject a syntax formatter
	 *
	 * Only to be used by syntax parsers.
	 *
	 * @param TypoScriptFormatterInterface The formatter.
	 * @return void
	 */
	public function injectFormatter($formatter)
	{
		$this->formatter = $formatter;
	}

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
	public abstract function parse();

}

