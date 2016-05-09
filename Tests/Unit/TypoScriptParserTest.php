<?php

require_once("vendor/autoload.php");

class TypoScriptParserTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->parser = new \ElmarHinz\TypoScriptParser();
	}

	/**
	 * @dataProvider tsProvider
	 */
	public function testParse($expected, $input)
	{
		$result = $this->parser->parse($input);
		$this->assertEquals($expected, $result);
	}

	public function tsProvider()
	{
		return [
			array (
				[
				],
				[
					'// double slash comment',
					'/ single slash comment',
				],
			),
			array (
				[
					'one.' => [
						'two.' => [
							'three' => 'FOUR'
						]
					]
				],
				[
					'one.two.three = FOUR',
				],
			),
			array (
				[
					'one.' => [
						'two.' => [
							'three' => 'FOUR'
						]
					]
				],
				[
					'one.two { ',
					'	three = FOUR',
					'} ',
				],
			),
		];
	}

}

