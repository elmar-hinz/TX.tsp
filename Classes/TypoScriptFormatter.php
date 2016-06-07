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
		= '<pre class="ts-hl">%s%s</pre>';
	const TOKEN_FORMAT
		= '<span class="%s">%s</span>';
	const ERROR_FORMAT
		= ' <span class="ts-error"><strong> ERROR:</strong> %s</span>';
	const EXTENDED_ERROR_FORMAT
		= ' <span class="ts-error"><strong> ERROR (line %s):</strong> %s</span>';
    const TEMPLATE_ERROR_FORMAT
        =  '          <span class="ts-error"><strong> ERROR AT END OF TEMPLATE:</strong> %s</span>';
	const LINE_FORMAT
		= '%s%s%s';
	const LINE_NUMBER_FORMAT
		= '<span class="ts-linenum">%4d</span> ';
	const EXTENDED_LINE_NUMBER_FORMAT
		= '<span class="ts-linenum">%4d|%04d</span> ';

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
    const VALID_KEY_MISSING_FORMAT
        = 'Missng valid key, limited to alphanumeric and ".-_\\".';
    const VALID_OPERATOR_MISSING_FORMAT
        = 'Missing valid operator, one of "=<>{(" or ":=".';

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
		= 'The upper bound of arguments is four.';

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
        AP::VALID_KEY_MISSING_ERROR
        => self::VALID_KEY_MISSING_FORMAT,
        AP::VALID_OPERATOR_MISSING_ERROR
        => self::VALID_OPERATOR_MISSING_FORMAT,
	];

	/**
	 * Hide line numbers
	 */
	protected $hideLineNumbers = false;

	/**
	 * Number to count from
	 */
	protected $numberOfBaseLine = 1;

	/**
	 * Number of last line seen
	 */
	protected $numberOfLastLineSeen = null;

	/**
	 * Collect the tokens per line.
     *
     * $tokens[$lineNumber][]['class'] = $tokenClass;
     * $tokens[$lineNumber][]['value'] = $tokenValue
	 */
	protected $tokens = [];

	/**
	 * Collect the errors per line.
     *
     * $errors[$lineNumber][]['class'] = $errorClass;
     * $errors[$lineNumber][]['arguments'] = array(...);
	 */
	protected $errors = [];

	/**
	 * Collect the final errors
     *
     * $errors[$lineNumber]['class'] = $errorClass;
     * $errors[$lineNumber]['arguments'] = array(...);
	 */
	protected $finalErrors = [];

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
	public function setNumberOfBaseLine($number)
	{
		$this->numberOfBaseLine = $number;
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
		return $this->numberOfLastLineSeen;
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
        if($this->numberOfLastLineSeen)
            return $this->numberOfLastLineSeen - $this->numberOfBaseLine + 1;
        else
            return 0;
	}

	/**
	 * Push a token.
	 *
	 * The token classes are defined as constants in AbstractTypoScriptParser.
	 *
     * @see TypoScriptFormatterInterface::pushToken()
	 * @param integer $lineNumber the line number.
	 * @param integer $tokenClass The token class.
	 * @param string $string The token string.
	 * @return void
	 */
	public function pushToken($lineNumber, $tokenClass, $token)
	{
        $this->tokens[$lineNumber][]
            = array( 'class' => $tokenClass, 'value' => $token);
	}

	/**
	 * Push an error.
	 *
	 * The type and order of further arguments must matcht the $errorClass. In
	 * case there are further arguments this is documented with the error class
	 * constant in AbstractTypoScriptParser.
	 *
     * @see TypoScriptFormatterInterface::pushError()
	 * @param integer The line number.
     * @param integer Error class.
	 * @param mixed Further arguments.
	 * @return void
	 */
	public function pushError()
	{
        $arguments = func_get_args();
        $lineNumber = $arguments[0];
        $errorClass = $arguments[1];
        $furtherArguments = array_splice($arguments, 2);
        if(count($furtherArguments) > 2) {
            throw new \OutOfBoundsException(
                self::PUSH_ERROR_ARGUMENTS_EXCEPTION, 1484191758);
        }
        $this->errors[$lineNumber][]
            = ['class' => $errorClass, 'arguments' => $furtherArguments];
	}

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
	public function pushFinalError()
    {
        $arguments = func_get_args();
        $errorClass = $arguments[0];
        $furtherArguments = array_splice($arguments, 1);
        $this->finalErrors[]
            = ['class' => $errorClass, 'arguments' => $furtherArguments];
    }

    /**
     * Track the last line seen to find the number of lines.
     *
     * $lineNumber starts by 1 for the first line of the template.
     *
     * @param int $lineNumber The current line number.
     * @return void
     */
    public function finishLine($lineNumber) {
        $this->numberOfLastLineSeen
            = $this->numberOfBaseLine + $lineNumber - 1;
    }

	public function finish()
	{
        $lines = [];
        for($i = 1; $i <= $this->getCountOfLines(); $i++)
            $lines[] = $this->buildLine($i);
        $finalErrors = '';
        if(count($this->finalErrors) > 0) {
            $errors = [];
            foreach($this->finalErrors as $error)
                $errors[] = $this->buildErrorMessage($error);
            $finalErrors = "\n" . sprintf(
                self::TEMPLATE_ERROR_FORMAT, implode(' ' , $errors));
        }
        return sprintf(self::COMPOSE_FORMAT,
            implode("\n", $lines), $finalErrors);
	}

    /**
     * Create a string of the given line.
     *
     * @param integer $lineNumber Line number starting with one.
     * @return string The line.
     */
	protected function buildLine($lineNumber)
	{
		$tokens = '';
		if(array_key_exists($lineNumber, $this->tokens)) {
            $tokens = [];
            foreach($this->tokens[$lineNumber] as $token)
                $tokens[] = $this->buildToken($token);
			$tokens = implode('', $tokens);
		}
		$errors = '';
		if(array_key_exists($lineNumber, $this->errors)) {
            $errors = [];
            foreach($this->errors[$lineNumber] as $error)
                $errors[] = $this->buildErrorMessage($error);
            if($this->hideLineNumbers) {
                $errors = sprintf(self::EXTENDED_ERROR_FORMAT,
                    $lineNumber, implode(' ', $errors));
            } else {
                $errors = sprintf(self::ERROR_FORMAT, implode(' ', $errors));
            }
		}
        $nr = '';
        if(!$this->hideLineNumbers) {
            if($this->numberOfBaseLine == 1) {
                $nr = sprintf(self::LINE_NUMBER_FORMAT, $lineNumber);
            } else {
                $fullNumber = $this->numberOfBaseLine + $lineNumber - 1;
                $nr = sprintf(self::EXTENDED_LINE_NUMBER_FORMAT,
                    $lineNumber, $fullNumber);
            }
        }
	    return sprintf(self::LINE_FORMAT, $nr, $tokens, $errors);
	}

    protected function buildToken($token)
    {
		$class = $this->tokenToClassMap[$token['class']];
        return sprintf(self::TOKEN_FORMAT, $class,
            htmlspecialchars($token['value']));
    }

    protected function buildErrorMessage($error)
    {
		$format = $this->errorToMessageMap[$error['class']];
        $arguments = $error['arguments'];
		switch(count($arguments)) {
		case 0:
			$message = sprintf($format);
			break;
		case 1:
			$message = sprintf($format, $arguments[0]);
			break;
		case 2:
			$message = sprintf($format, $arguments[0], $arguments[1]);
			break;
		default:
			throw new \OutOfBoundsException(
				self::PUSH_ERROR_ARGUMENTS_EXCEPTION, 1484191758);
		}
        return $message;
    }
}

