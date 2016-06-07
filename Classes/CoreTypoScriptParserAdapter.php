<?php

namespace ElmarHinz\TypoScriptParser;

use	\TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser as CoreParser;
use	\ElmarHinz\TypoScriptParser\TypoScriptProductionParser as ProductionParser;
use	\ElmarHinz\TypoScriptParser\TypoScriptConditionsProcessor as ConditionsProcessor;
use	\ElmarHinz\TypoScriptParser\TypoScriptSyntaxParser as SyntaxParser;
use	\ElmarHinz\TypoScriptParser\TypoScriptFormatter as Formatter;

class CoreTypoScriptParserAdapter extends CoreParser implements ValueModifierInterface
{

    public function parse($string, $matchObj = '')
    {
		$preProcessor = new ConditionsProcessor();
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
            $formatter->setNumberOfBaseLine($numbers[0]);
        } else {
            $formatter->hideLineNumbers();
        }
		$parser = new SyntaxParser();
        $parser->injectFormatter($formatter);
        $parser->appendTemplate($template);
        return $parser->parse();
    }

}

