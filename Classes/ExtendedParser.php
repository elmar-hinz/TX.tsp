<?php

namespace ElmarHinz;

use	\TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser as OldParser;
use	\ElmarHinz\TypoScriptParser as NewParser;
use	\ElmarHinz\TypoScriptPreProcessor as PreProcessor;

class ExtendedParser extends OldParser
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

