<?php

namespace ElmarHinz\TypoScript;

use	\TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser as CoreParser;
use	\ElmarHinz\TypoScript\TypoScriptParser as NewParser;
use	\ElmarHinz\TypoScript\TypoScriptPreProcessor as PreProcessor;

class CoreTypoScriptParserAdapter extends CoreParser
{

    public function parse($string, $matchObj = '')
    {
		$preProcessor = new PreProcessor();
		$parser = new NewParser();
		$preProcessor->setMatcher($matchObj);
		$preProcessor->appendTemplate($string);
		$parser->appendTemplate($preProcessor->parse());
		$this->setup = $parser->parse();
	}

}

