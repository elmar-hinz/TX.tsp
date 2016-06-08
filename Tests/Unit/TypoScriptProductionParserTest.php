<?php

namespace ElmarHinz\TypoScriptParser\Tests\Unit;

use \ElmarHinz\TypoScriptParser\Tests\Unit\Fixtures\TypoScriptExamples as Examples;
use \ElmarHinz\TypoScriptParser\TypoScriptProductionParser as Parser;
use \ElmarHinz\TypoScriptParser\ValueModifierInterface as ValueModifier;
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

