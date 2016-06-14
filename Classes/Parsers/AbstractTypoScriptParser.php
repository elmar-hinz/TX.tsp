<?php

namespace ElmarHinz\TypoScriptParser\Parsers;

use ElmarHinz\TypoScriptParser\Interfaces\TypoScriptParserInterface;

abstract class AbstractTypoScriptParser implements TypoScriptParserInterface
{

	/*******************************************************
	 * Constants
	 *******************************************************/

	const DOT = '.';
	const NL = "\n";
	const EMPTY_STRING = '';

	/*******************************************************
	 * Regular expressions to tokenize TypoScript
	 *******************************************************/

	const COMMENT_CONTEXT_CLOSE_REGEX = '|^(\s*)(\*/)(.*)$|';
	const COMMENT_CONTEXT_OPEN_REGEX = '|^(\s*)(/\*)(.*)$|';
	const COMMENT_REGEX = '/^(\s*)(#|\/[^\*])(.*)$/';
	const CONDITION_REGEX = '|^(\s*)(\[.*)$|';
	const LEVEL_CLOSE_REGEX = '|^(\s*)(})(.*)$|';
	const OPERATOR_REGEX = '/^(\s*)([[:alnum:].\\\\_-]*[[:alnum:]\\\\_-])(\s*)(:=|[=<>{(])(\s*)(.*)$/';
	const VALUE_CONTEXT_OPEN_REGEX = '/^(\s*)([[:alnum:].\\\\_-]*[[:alnum:]\\\\_-])(\s*)[(].*$/';
	const VALUE_CONTEXT_CLOSE_REGEX = '|^(\s*)(\))(.*)$|';
	const VOID_REGEX = '|^\s*$|';

	/*******************************************************
	 * Regular expressions to debug TypoSciript
	 *******************************************************/

    /* Valid Key:
     *
     * Terminated by:
     *
     * EOL
     * whitespace
     * valid operator
     */
    const VALID_KEY_REGEX = '/^(\s*)([[:alnum:].\\\\_-]*[[:alnum:]\\\\_-])((\s|:=|[=<>{(]).*)?$/';

    const VALID_OPERATOR_REGEX = '/(:=|[=<>{(])/';

	/*******************************************************
	 * TypoSciript operators
	 *******************************************************/

	const ASSIGN_OPERATOR = '=';
	const COPY_OPERATOR = '<';
	const LEVEL_OPEN_OPERATOR = '{';
	const MODIFY_OPERATOR = ':=';
	const UNSET_OPERATOR = '>';
	const VALUE_CONTEXT_OPEN_OPERATOR = '(';

	/*******************************************************
	 * TypoSciript multiline contexts
	 *******************************************************/

	const COMMENT_CONTEXT = 1;
	const DEFAULT_CONTEXT = 2;
	const VALUE_CONTEXT   = 3;

	/*******************************************************
	 * Syntax token classes
	 *******************************************************/

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

	/*******************************************************
	 * Errors
	 *******************************************************/

	/**
	 * Unexpected closing brace.
	 */
	const NEGATIVE_KEYS_LEVEL_ERROR = 1;

	/**
	 * Braces are not closed at condition.
	 *
	 * pushToken() parameters:
	 *
	 * @param: integer The brace level.
	 */
	const POSITIVE_KEYS_LEVEL_AT_CONDITION_ERROR = 2;

	/**
	 * Braces are not closed at end of template.
	 *
	 * pushToken() parameters:
	 *
	 * @param: integer The brace level.
	 */
	const POSITIVE_KEYS_LEVEL_AT_END_ERROR  = 3;

	/**
	 * Multiline comment not closed at end of template.
	 */
	const UNCLOSED_COMMENT_CONTEXT_ERROR= 4;

	/**
	 * Multiline value not closed at end of template.
	 */
	const UNCLOSED_VALUE_CONTEXT_ERROR = 5;

    /**
     * Invalid line in given context.
     */
    const INVALID_LINE_ERROR = 6;

    /**
     * A valid key is missing.
     */
    const VALID_KEY_MISSING_ERROR = 7;

    /**
     * A valid operator is missing.
     */
    const VALID_OPERATOR_MISSING_ERROR = 8;

	/*******************************************************
	 * Instance variables
	 *******************************************************/

	/**
	 * The lines to parse.
	 */
	protected $inputLines = Null;

	/*******************************************************
	 * Methods
	 *******************************************************/

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
            for($i = 0; $i < count($template); $i++) {
                if(substr($template[$i], -1) == "\r")
                    $template[$i] = substr($template[$i], 0, -1);
            }
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

}
