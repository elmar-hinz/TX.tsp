<?php

namespace ElmarHinz\TypoScriptParser;

use \ElmarHinz\TypoScriptParser\AbstractTypoScriptParser as AP;

/**
 * TypoScript syntax formatter
 *
 * Responsible for:
 *
 * - Error tracking
 * - Error formatting
 * - Token formatting
 * - Line formatting
 * - Final document formatting
 * - Line number tracking
 *
 * If a number for the first line is not set, the first line is 1.
 *
 * @see: TypoScriptFormatterInterface
 */
class TypoScriptFormatter implements TypoScriptFormatterInterface
{
	/**
	 * Formatter strings
	 */
	const COMPOSE_FORMAT
		= '<pre class="ts-hl">%s</pre>';
	const TOKEN_FORMAT
		= '<span class="%s">%s</span>';
	const ERROR_FORMAT
		= ' <span class="ts-error"><strong> - ERROR:</strong> %s</span>';
	const FINAL_ERROR_FORMAT
		=  '      <span class="ts-error"><strong> - FINAL ERROR:</strong> %s</span>';
	const LINE_FORMAT
		= '%s%s%s';
	const LINE_NUMBER_FORMAT
		= '<span class="ts-linenum">%4d:</span> ';

    const INVALID_LINE_FORMAT
        = 'The syntax of this line is invalid.';
    const NEGATIVE_KEYS_LEVEL_FORMAT
        = 'A closing brace in excess.';
	const POSITIVE_KEYS_LEVEL_AT_CONDITION_FORMAT
		= '%d closing brace(s) missing at condition.';
	const POSITIVE_KEYS_LEVEL_AT_END_FORMAT
		= '%d closing brace(s) missing.';
    const UNCLOSED_COMMENT_CONTEXT_FORMAT
        = 'Unclosed multiline comment.';
    const UNCLOSED_VALUE_CONTEXT_FORMAT
        = 'Unclosed multiline value.';


	/**
	 * CSS classes of highligthed elements and errors
	 */
	const COMMENT_CLASS            = 'ts-comment';
	const CONDITION_CLASS          = 'ts-condition';
	const DEFAULT_CLASS            = 'ts-default';
	const IGNORED_CLASS            = 'ts-ignored';
	const KEYS_CLASS               = 'ts-objstr';
	const KEYS_POSTSPACE_CLASS     = 'ts-objstr_postspace';
	const OPERATOR_CLASS           = 'ts-operator';
	const OPERATOR_POSTSPACE_CLASS = 'ts-operator_postspace';
	const PRESPACE_CLASS           = 'ts-prespace';
	const VALUE_CLASS              = 'ts-value';
	const VALUE_COPY_CLASS         = 'ts-value_copy';

	/**
	 * Exceptions
	 */
	const PUSH_ERROR_ARGUMENTS_EXCEPTION
		= 'The upper bound of arguments is three.';

	/**
	 * Token to class map
	 */
	protected $tokenToClassMap = [
		AP::COMMENT_CONTEXT_TOKEN => self::COMMENT_CLASS,
		AP::COMMENT_TOKEN => self::COMMENT_CLASS,
		AP::CONDITION_TOKEN => self::CONDITION_CLASS,
		AP::IGNORED_TOKEN => self::IGNORED_CLASS,
		AP::KEYS_POSTSPACE_TOKEN => self::KEYS_POSTSPACE_CLASS,
		AP::KEYS_TOKEN => self::KEYS_CLASS,
		AP::OPERATOR_POSTSPACE_TOKEN => self::OPERATOR_POSTSPACE_CLASS,
		AP::OPERATOR_TOKEN => self::OPERATOR_CLASS,
		AP::PRESPACE_TOKEN => self::PRESPACE_CLASS,
		AP::VALUE_CONTEXT_TOKEN => self::VALUE_CLASS,
		AP::VALUE_COPY_TOKEN => self::VALUE_COPY_CLASS,
		AP::VALUE_TOKEN => self::VALUE_CLASS,
	];

	/**
	 * Error to message map
	 */
	protected $errorToMessageMap = [
        AP::INVALID_LINE_ERROR
        => self::INVALID_LINE_FORMAT,
        AP::NEGATIVE_KEYS_LEVEL_ERROR
        => self::NEGATIVE_KEYS_LEVEL_FORMAT,
        AP::POSITIVE_KEYS_LEVEL_AT_CONDITION_ERROR
        => self::POSITIVE_KEYS_LEVEL_AT_CONDITION_FORMAT,
        AP::POSITIVE_KEYS_LEVEL_AT_END_ERROR
        => self::POSITIVE_KEYS_LEVEL_AT_END_FORMAT,
        AP::UNCLOSED_COMMENT_CONTEXT_ERROR
        => self::UNCLOSED_COMMENT_CONTEXT_FORMAT,
        AP::UNCLOSED_VALUE_CONTEXT_ERROR
        => self::UNCLOSED_VALUE_CONTEXT_FORMAT,
	];

	/**
	 * Hide line numbers
	 */
	protected $hideLineNumbers = false;

	/**
	 * Number of first line
	 */
	protected $numberOfFirstLine = 1;

	/**
	 * Line counter
	 */
	protected $lineCounter = 0;

	/**
	 * Collect the elements of the current line.
	 */
	protected $currentElements = [];

	/**
	 * Collect the errors of the current line or the final errors.
	 */
	protected $currentErrors = [];

	/**
	 * Collect the lines.
	 */
	protected $lines = [];

	/**
     * Hide line numbers
     *
     * @return void
     */
    public function hideLineNumbers()
    {
        $this->hideLineNumbers = true;
    }

	/**
     * Show line numbers
     *
     * @return void
     */
    public function showLineNumbers()
    {
        $this->hideLineNumbers = false;
    }

	/**
	 * Set number first line.
	 *
	 * If called, it shall be called before parsing.
	 *
	 * @param integer The line number.
	 * @return void
	 */
	public function setNumberOfFirstLine($number)
	{
		$this->numberOfFirstLine = $number;
	}

	/**
	 * Get the number of the last line.
	 *
	 * If lines start with 1 it is equal to the number of lines.
	 * To be called after parsing.
	 *
	 * @return integer The line number.
	 */
	public function getNumberOfLastLine()
	{
		return $this->numberOfFirstLine + $this->lineCounter - 1;
	}

	/**
	 * Get the number of lines.
	 *
	 * Count of all lines.
	 * To be called after parsing.
	 *
	 * @return integer The count of lines.
	 */
	public function getCountOfLines()
	{
		return $this->lineCounter;
	}

	public function pushToken($tokenClass, $element)
	{
		$class = $this->tokenToClassMap[$tokenClass];
		$format = self::TOKEN_FORMAT;
		$element = htmlspecialchars($element);
		$this->currentElements[] = sprintf($format, $class, $element);
	}

	/**
	 * Push an error message fo the current line.
	 *
	 * The type and order of further arguments must matcht the $errorClass. In
	 * case there are further arguments this is documented with the error class
	 * constant in AbstractTypoScriptParser.
	 *
     * @see TypoScriptFormatterInterface::pushError()
	 * @param string The error message.
	 * @param mixed Further arguments.
	 * @return void
	 */
	public function pushError()
	{
        $arguments = func_get_args();
        $errorClass = $arguments[0];
        $furtherArguments = array_splice($arguments, 1);
		$format = $this->errorToMessageMap[$errorClass];
		switch(count($furtherArguments)) {
		case 0:
			$message = sprintf($format);
			break;
		case 1:
			$message = sprintf($format, $furtherArguments[0]);
			break;
		case 2:
			$message = sprintf($format,
				$furtherArguments[0], $furtherArguments[1]);
			break;
		default:
			throw new \OutOfBoundsException(
				self::PUSH_ERROR_ARGUMENTS_EXCEPTION, 1484191758);
		}
		$this->currentErrors[] = $message;
	}

	public function finishLine()
	{
		$elements = '';
		$errors = '';
		if($this->currentElements) {
			$elements = implode('', $this->currentElements);
		}
		if($this->currentErrors) {
			$errors = implode(' ', $this->currentErrors);
			$errors = sprintf(self::ERROR_FORMAT, $errors);
		}
        if($this->hideLineNumbers) {
            $nr = '';
        } else {
            $nr = $this->numberOfFirstLine + $this->lineCounter;
            $nr = sprintf(self::LINE_NUMBER_FORMAT, $nr);
        }
		$this->lines[] = sprintf(self::LINE_FORMAT, $nr, $elements, $errors);
		$this->currentElements = [];
		$this->currentErrors = [];
		$this->lineCounter++;
	}

	public function finish()
	{
		$errors = '';
		if($this->currentErrors) {
			$errors = implode(' ', $this->currentErrors);
			$errors = "\n" . sprintf(self::FINAL_ERROR_FORMAT, $errors);
		}
		return sprintf(self::COMPOSE_FORMAT,
			implode("\n", $this->lines) . $errors);
	}

}

