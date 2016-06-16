<?php

namespace ElmarHinz\TypoScriptParser\Formatters;

use ElmarHinz\TypoScriptParser\Interfaces\
    TypoScriptParsetimeExceptionTrackerPullInterface;
use ElmarHinz\TypoScriptParser\Interfaces\TypoScriptTokenTrackerPullInterface;

class TypoScriptSyntaxHighlightFormatter
{
	/**
	 * Format strings
	 */
	const DOCUMENT_FORMAT
		= '<pre class="ts-hl">%s%s</pre>';
	const EXCEPTIONS_FORMAT
		= ' <span class="ts-error"><strong> ERROR:</strong> %s</span>';
	const EXTENDED_EXCEPTIONS_FORMAT
		= ' <span class="ts-error"><strong> ERROR (line %s):</strong> %s</span>';
    const FINAL_EXCEPTIONS_FORMAT
        =  '          <span class="ts-error"><strong> ERRORS AT END OF TEMPLATE:</strong> %s</span>';
	const LINE_FORMAT = '%s%s%s';
    const NUMBER_WRAP_FORMAT = '<span class="ts-linenum">%s</span> ';
	const NUMBER_FORMAT = '%4d';
	const EXTENDED_NUMBER_FORMAT = '%4d|%04d';

    protected $exceptionTracker;
    protected $tokenTracker;

	/**
	 * Hide line numbers
	 */
	protected $hideLineNumbers = false;

	/**
	 * Number to count from
	 */
	protected $numberOfBaseLine = 1;

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
     * Inject the exception tracker.
     *
     * @param TypoScriptParsetimeExceptionTrackerPullInterface $tracker
     * @return void
     */
    public function injectExceptionTracker(
        TypoScriptParsetimeExceptionTrackerPullInterface $tracker
    )
    {
        $this->exceptionTracker = $tracker;
    }

    /**
     * Inject the token tracker.
     *
     * @param TypoScriptTokenTrackerPullInterface $tracker
     * @return void
     */
    public function injectTokenTracker(
        TypoScriptTokenTrackerPullInterface $tracker
    )
    {
        $this->tokenTracker = $tracker;
    }

    /**
     * Format and return the output.
     *
     * @return string The highlighted output.
     */
    public function format()
    {
        return sprintf(
            self::DOCUMENT_FORMAT,
            $this->buildLines(),
            $this->buildFinalExceptions()
        );
    }

    protected function buildLines()
    {
        $lines = [];
        foreach ($this->tokenTracker as $lineNumber => $tokens)
            $lines[] = $this->buildLine($lineNumber, $tokens);
        return implode("\n", $lines);
    }

    protected function buildLine($lineNumber, $tokens)
    {
        return sprintf(
            self::LINE_FORMAT,
            $this->buildLineNumber($lineNumber),
            $this->buildLineTokens($tokens),
            $this->buildLineExceptions($lineNumber)
        );
    }

    protected function buildLineNumber($lineNumber)
    {
        if($this->hideLineNumbers) {
            return '';
        } else {
            return sprintf(self::NUMBER_WRAP_FORMAT,
                $this->buildNumber($lineNumber));
        }
    }

    protected function buildNumber($lineNumber)
    {
        if($this->numberOfBaseLine == 1) {
            return sprintf(self::NUMBER_FORMAT, $lineNumber);
        } else {
            $fullNumber = $this->numberOfBaseLine + $lineNumber - 1;
            return sprintf(self::EXTENDED_NUMBER_FORMAT,
                $lineNumber, $fullNumber);
        }
    }

    protected function buildLineTokens($tokens)
    {
        $out = '';
        foreach($tokens as $token) $out .= $token->toTag();
        return $out;
    }

    protected function buildLineExceptions($lineNumber)
    {
        if($this->hideLineNumbers) {
            return $this->buildExceptions(
                self::EXTENDED_EXCEPTIONS_FORMAT,
                $this->exceptionTracker->getByLineNumber($lineNumber),
                trim($this->buildNumber($lineNumber))
            );
        } else {
            return $this->buildExceptions(
                self::EXCEPTIONS_FORMAT,
                $this->exceptionTracker->getByLineNumber($lineNumber)
            );
        }
    }

    protected function buildFinalExceptions()
    {
        return $this->buildExceptions(
            self::FINAL_EXCEPTIONS_FORMAT,
            $this->exceptionTracker->getFinalExceptions()
        );
    }

    protected function buildExceptions($format, $exceptions, $nr = null)
    {
        if ($exceptions) {
            $messages = [];
            foreach ($exceptions as $e) $messages[] = $e->getMessage();
            if($nr)
                return sprintf($format, $nr, implode(' ', $messages));
            else
                return sprintf($format, implode(' ', $messages));
        } else {
            return '';
        }
    }
}
