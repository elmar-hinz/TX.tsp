<?php

namespace ElmarHinz\TypoScript;

use	\TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser as CoreParser;
use	\ElmarHinz\TypoScript\TypoScriptParser as NewParser;
use	\ElmarHinz\TypoScript\TypoScriptPreProcessor as PreProcessor;

class CoreTypoScriptParserAdapter extends CoreParser implements ValueModifierInterface
{

    public function parse($string, $matchObj = '')
    {
		$preProcessor = new PreProcessor();
		$preProcessor->setMatcher($matchObj);
		$parser = new NewParser();
		$parser->setValueModifier($this);
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

}

