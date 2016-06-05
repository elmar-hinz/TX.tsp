<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit;

use \ElmarHinz\TypoScriptParser\Tests\Unit\Fixtures\TypoScriptExamples as Examples;
use \ElmarHinz\TypoScriptParser\ProductionTypoScriptParser as Parser;

class ProductionTypoScriptParserTest extends \PHPUnit_Framework_TestCase
{
	const MODIFIER_INTERFACE = '\\ElmarHinz\\TypoScriptParser\\ValueModifierInterface';

	public function setup()
	{
		$modifier = $this->getMockBuilder(self::MODIFIER_INTERFACE)->getMock();
		$modifier->method('modifyValue')->willReturn(
			'pre_value');
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

