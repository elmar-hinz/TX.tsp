<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit\Parsers;

use ElmarHinz\TypoScriptParser\Parsers\TypoScriptProductionParser
    as Parser;
use ElmarHinz\TypoScriptParser\Interfaces\TypoScriptValueModifierInterface
    as ValueModifier;
use ElmarHinz\TypoScriptParser\Tests\Unit\Fixtures\TypoScriptExamples
    as Examples;

class TypoScriptProductionParserTest extends \PHPUnit_Framework_TestCase
{

	public function setup()
	{
		$modifier = $this->getMockBuilder(ValueModifier::class)->getMock();
		$modifier->method('modifyValue')->willReturn('pre_value');
		$this->parser = new Parser();
		$this->parser->setValueModifier($modifier);
	}

	public function tsProvider()
	{
		return Examples::getExamples();
	}

	/**
	 * @dataProvider tsProvider
	 * @test
	 */
	public function parseTyposcript($input, $tree)
	{
		$this->parser->appendTemplate($input);
		$result = $this->parser->parse();
		$this->assertSame($tree, $result);
	}

}

