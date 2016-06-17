<?php

namespace ElmarHinz\TypoScriptParser\Main;

use	TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser
    as CoreParser;
use ElmarHinz\TypoScriptParser\Interfaces\TypoScriptValueModifierInterface
    as Modificator;
use	ElmarHinz\TypoScriptParser\Parsers\TypoScriptProductionParser
    as ProductionParser;
use	ElmarHinz\TypoScriptParser\Parsers\TypoScriptConditionsPreProcessor
    as ConditionsPreProcessor;
use	ElmarHinz\TypoScriptParser\Parsers\TypoScriptSyntaxParser
    as SyntaxParser;
use	ElmarHinz\TypoScriptParser\Formatters\TypoScriptSyntaxHighlightFormatter
    as Formatter;
use	ElmarHinz\TypoScriptParser\Trackers\TypoScriptParsetimeExceptionTracker
    as ExceptionTracker;
use	ElmarHinz\TypoScriptParser\Trackers\TypoScriptTokenTracker
    as TokenTracker;

class CoreTypoScriptParserAdapter extends CoreParser
    implements Modificator
{

    public function parse($string, $matchObj = '')
    {
		$preProcessor = new ConditionsPreProcessor();
		$preProcessor->setMatcher($matchObj);
		$parser = new ProductionParser();
		$parser->setValueModifier($this);
		$parser->presetTree($this->setup);
		$preProcessor->appendTemplate($string);
		$parser->appendTemplate($preProcessor->parse());
		$this->setup = $parser->parse();
	}

	public function modifyValue($value, $operation)
	{
		$pattern = '/^([[:alpha:]]+)\\s*\\((.*)\\).*/';
		if (preg_match($pattern, $operation, $matches)) {
			list(,$modifier, $argument) = $matches;
            return (string)$this->executeValueModifier(
                $modifier, $argument, $value);
		} else {
			// Error handling: not well formatted modifier
		}
	}

    /**
     * Do syntax highlighting
     *
     * @param string The template to parse.
     * @param array The first entry is the line number offset.
     * @param bool Toggle blockmode.
     */
    public function doSyntaxHighlight($template, $numbers = null,
        $blockmode = false)
    {
        $exceptionTracker = new ExceptionTracker();
        $tokenTracker = new TokenTracker();
        $formatter = new Formatter();
        $formatter->injectExceptionTracker($exceptionTracker);
        $formatter->injectTokenTracker($tokenTracker);
        if (is_array($numbers) && count($numbers) > 0
            && is_int($numbers[0])) {
            $formatter->setNumberOfBaseLine($numbers[0]);
        } else {
            $formatter->hideLineNumbers();
        }
		$parser = new SyntaxParser();
        $parser->injectExceptionTracker($exceptionTracker);
        $parser->injectTokenTracker($tokenTracker);
        $parser->appendTemplate($template);
        $parser->parse();
        return $formatter->format();
    }

}

