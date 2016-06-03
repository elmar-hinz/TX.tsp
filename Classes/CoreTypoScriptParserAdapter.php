<?php

namespace ElmarHinz\TypoScript;

use	\TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser as CoreParser;
use	\ElmarHinz\TypoScript\TypoScriptParser as NewParser;
use	\ElmarHinz\TypoScript\TypoScriptConditionsProcessor as ConditionsProcessor;
use	\ElmarHinz\TypoScript\TypoScriptSyntaxParser as SyntaxParser;
use	\ElmarHinz\TypoScript\TypoScriptFormatter as Formatter;

class CoreTypoScriptParserAdapter extends CoreParser implements ValueModifierInterface
{

    public function parse($string, $matchObj = '')
    {
		$preProcessor = new ConditionsProcessor();
		$preProcessor->setMatcher($matchObj);
		$parser = new NewParser();
		$parser->setValueModifier($this);
		$parser->presetTree($this->setup);
		$preProcessor->appendTemplate($string);
		$parser->appendTemplate($preProcessor->parse());
		$this->setup = $parser->parse();
	}

	public function modifyValue($value, $operation)
	{
		$pattern = '/^([[:alpha:]]+)\\s*\\((.*)\\).*/';
		if(preg_match($pattern, $operation, $matches)) {
			list(,$modifier, $argument) = $matches;
				return (string)$this->executeValueModifier($modifier, $argument, $value);
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
    public function doSyntaxHighlight($template, $numbers = null, $blockmode = false)
    {
        $formatter = new Formatter();
        if(is_array($numbers) && count($numbers) > 0 && is_int($numbers[0])) {
            $formatter->setNumberOfFirstLine($numbers[0]);
        } else {
            $formatter->hideLineNumbers();
        }
		$parser = new SyntaxParser();
        $parser->injectFormatter($formatter);
        $parser->appendTemplate($template);
        return $parser->parse();
    }

}

