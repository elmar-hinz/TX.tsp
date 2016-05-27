<?php

namespace ElmarHinz\Tests\Unit\Fixtures;

class FirstTypoScriptExamples {

	static public function getExamples() {
		return [
			'comments' => array (
				[
					'// double slash comment',
				],
				[
				],
				[
				],
			),
			'simple path' => array (
				[
					'one.two.three = FOUR',
				],
				[
					'one.two.three' => 'FOUR',
				],
				[
					'one.' => [
						'two.' => [
							'three' => 'FOUR'
						]
					]
				],
			),
			'nested path' => array (
				[
					'one.two { ',
					'	three = FOUR',
					'} ',
				],
				[
					'one.two.three' => 'FOUR',
				],
				[
					'one.' => [
						'two.' => [
							'three' => 'FOUR'
						]
					]
				],
			),
			'override' => array (
				[
					'one.two = THREE',
					'one.two = FIVE',
				],
				[
					'one.two' => 'FIVE',
				],
				[
					'one.' => [
						'two' => 'FIVE',
					]
				],
			),
			'nested override' => array (
				[
					'one.two { ',
					'	three = FOUR',
					'} ',
					'one { ',
					'	two.three = FIVE',
					'} ',
				],
				[
					'one.two.three' => 'FIVE',
				],
				[
					'one.' => [
						'two.' => [
							'three' => 'FIVE'
						]
					]
				],
			),
			'composition' => array (
				[
					'// double slash comment',
					'one.two = THREE',
					'one.two { ',
					'	three = FOUR',
					'} ',
					'one.two = FIVE',
					'one { ',
					'	two.three = FIVE',
					'} ',
				],
				[
					'one.two' => 'FIVE',
					'one.two.three' => 'FIVE',
				],
				[
					'one.' => [
						'two' => 'FIVE',
						'two.' => [
							'three' => 'FIVE'
						]
					]
				],
			),
		];
	}

}
