<?php

require_once("vendor/autoload.php");

define("LF", "\n");
define("TAB", "\t");

class TypoScriptParserTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->thisParser = new \ElmarHinz\TypoScriptParser();
		$this->t3Parser = new \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
	}

	/**
	 * @dataProvider tsProvider
	 */
	public function testCmsParser($expected, $input)
	{
		$input = implode("\n", $input);
		$this->t3Parser->parse($input);
		$result = $this->t3Parser->setup;
		$this->assertEquals($expected, $result);
	}

	/**
	 * @dataProvider tsProvider
	 */
	public function testParse($expected, $input)
	{
		$result = $this->thisParser->parse($input);
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
			array (
				[
					'one.' => [
						'two' => 'THREE',
						'two.' => [
							'three' => 'FOUR'
						]
					]
				],
				[
					'/ single slash comment',
					'one.two = THREE',
					'one.two { ',
					'	three = FOUR',
					'} ',
				],
			),
		];
	}

}

