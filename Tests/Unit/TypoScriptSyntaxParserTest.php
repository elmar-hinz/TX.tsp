<?php

namespace ElmarHinz\TypoScript\Tests\Unit;

use \ElmarHinz\TypoScript\Tests\Unit\Fixtures\TypoScriptExamples as Examples;
use \ElmarHinz\TypoScript\TypoScriptSyntaxParser as Parser;

class TypoScriptSyntaxParserTest extends \PHPUnit_Framework_TestCase
{
	/* const MODIFIER_INTERFACE = '\\ElmarHinz\\TypoScript\\ValueModifierInterface'; */

	public function setup()
	{
		/* $modifier = $this->getMockBuilder(self::MODIFIER_INTERFACE)->getMock(); */
		/* $modifier->method('modifyValue')->willReturn('pre_value'); */
		$this->parser = new Parser();
		/* $this->parser->setValueModifier($modifier); */
	}

	public function tsProvider()
	{
		return [
			'empty TS' => [ ['ts-hl'], '' ],
			'simple assignment' => [
				[
					'one', '1',
					'ts-hl',
					'ts-linenum',
					'ts-prespace',
					'ts-objstr',
					'ts-objstr_postspace',
					'ts-operator',
					'ts-operator_postspace',
					'ts-value',
				],
				[ 'one = 1' ],
			],
			'modify' => [
				[ 'ts-operator', 'ts-value', ],
				[ 'one := append(xxx)' ],
			],
			'copy' => [
				[ 'ts-operator', 'ts-value_copy', ],
				[ 'one < .two' ],
			],
			'delete' => [
				[ 'ts-objstr', 'ts-operator', 'ts-ignored', ],
				[ 'one > excess' ],
			],
			'mulitline value' => [
				[
					'ts-prespace',
					'ts-objstr',
					'ts-operator',
					'<span class="ts-ignored">excess1</span>',
					'ts-value',
					'<span class="ts-ignored">excess2</span>',
				],
				[
					' key (  excess1',
					'     content line  ',
					'     content line  ',
					' ) excess2',
				]
			],
			'comment' => [
				[ '# comment', 'ts-prespace', 'ts-comment', ],
				[ '# comment' ],
			],
			'mulitline comment' => [
				[
					'ts-prespace', 'ts-comment', 'ts-operator_postspace',
					'<span class="ts-ignored">excess</span>',
				],
				[
					' /*  opening line',
					'     content line',
					' */  excess',
				]
			],
		];
	}

	/**
	 * @dataProvider tsProvider
	 * @test
	 */
	public function parseTyposcript($expectations, $typoScript)
	{
		$this->parser->appendTemplate($typoScript);
		$result = $this->parser->parse();
		foreach ($expectations as $contains)
			$this->assertContains($contains, $result);
	}

}

