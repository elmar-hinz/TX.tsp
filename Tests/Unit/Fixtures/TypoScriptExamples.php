<?php

namespace ElmarHinz\TypoScript\Tests\Unit\Fixtures;

class TypoScriptExamples {

	static public function getExamples() {
		return [
			'comments' => array (
				[
					'// double slash comment',
				],
				[
				],
			),
			'simple path' => array (
				[
					'one.two.three = FOUR',
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
					'one.' => [
						'two' => 'FIVE',
					]
				],
			),
			'unsetting' => array (
				[
					'one.two = 2',
					'one.two.three = 3',
					'one.four = 4',
					'one.two >',
				],
				[
					'one.' => [
						'four' => '4',
					],
				],
			),
			'copying' => array (
				[
					'one.two = 2',
					'one.two.three = 3',
					'one.four < one.two',
					'one.two = 22',
					'one.two.three = 33',
				],
				[
					'one.' => [
						'two' => '22',
						'two.' => [ 'three' => '33', ],
						'four' => '2',
						'four.' => [ 'three' => '3', ],
					],
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
					'one.' => [
						'two.' => [
							'three' => 'FIVE'
						]
					]
				],
			),
			'modify' => array (
				[
					'value = value',
					'value := prependString(pre_)',
				],
				[
					'value' => 'pre_value',
				]
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

	static public function getPreProcessExamples() {
		 return [
			'false-false'  => array (
				[
					'before = 0',
					'[FALSE]',
						'condition.1 = 1',
					'[FALSE]',
						'condition.2 = 2',
					'[GLOBAL]',
					'after = 99',
				],
				[
					'before' => '0',
					'after' => '99',
				],
			),
			'false-true'  => array (
				[
					'before = 0',
					'[FALSE]',
						'condition.1 = 1',
					'[TRUE]',
						'condition.2 = 2',
					'[GLOBAL]',
					'after = 99',
				],
				[
					'before' => '0',
					'after' => '99',
					'condition.' => ['2' => '2'],
				],
			),
			'true-false'  => array (
				[
					'before = 0',
					'[TRUE]',
						'condition.1 = 1',
					'[FALSE]',
						'condition.2 = 2',
					'[GLOBAL]',
					'after = 99',
				],
				[
					'before' => '0',
					'after' => '99',
					'condition.' => ['1' => '1'],
				],
			),
			'true-true'  => array (
				[
					'before = 0',
					'[TRUE]',
						'condition.1 = 1',
					'[TRUE]',
						'condition.2 = 2',
					'[GLOBAL]',
					'after = 99',
				],
				[
					'before' => '0',
					'after' => '99',
					'condition.' => [
						'1' => '1',
						'2' => '2'
					],
				],
			),
			'false-else'  => array (
				[
					'before = 0',
					'[FALSE]',
						'condition.1 = 1',
					'[ELSE]',
						'condition.2 = 2',
					'[GLOBAL]',
					'after = 99',
				],
				[
					'before' => '0',
					'after' => '99',
					'condition.' => [
						'2' => '2'
					],
				],
			),
			'true-else'  => array (
				[
					'before = 0',
					'[TRUE]',
						'condition.1 = 1',
					'[ELSE]',
						'condition.2 = 2',
					'[GLOBAL]',
					'after = 99',
				],
				[
					'before' => '0',
					'after' => '99',
					'condition.' => [
						'1' => '1'
					],
				],
			),
			'complex'  => array (
				[
					'[TRUE]',
						'// double slash comment',
						'one.two = THREE',
						'one.two { ',
						'	three = FOUR',
						'} ',
						'one.two = FIVE',
						'one { ',
						'	two.three = FIVE',
						'} ',
					'[FALSE]',
						'never.seen = SORRY',
					'[ELSE]',
						'else = PREVIOUS FAILED',
					'[GLOBAL]',
				],
				[
					'one.' => [
						'two' => 'FIVE',
						'two.' => [
							'three' => 'FIVE'
						]
					],
					'else' => 'PREVIOUS FAILED',
				],
			),
		];
	}

}
