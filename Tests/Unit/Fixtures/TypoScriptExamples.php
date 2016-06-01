<?php

namespace ElmarHinz\TypoScript\Tests\Unit\Fixtures;

class TypoScriptExamples {

	static public function getExamples() {
		return [
			'comments' => array (
				[
					'/ single slash comment',
					'// double slash comment',
					'# hash comment',
				],
				[
				],
			),
			'multiline comments' => array (
				[
					'before = comment',
					'/* comment begin ',
				    '  comment.looks.like = path',
				    '  comment.looks (',
					'          like',
					'          multiline value',
					'  )',
					'*/ comment end discared ',
					'after = comment',
				],
				[
					'before' => 'comment',
					'after' => 'comment',
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
			'minus underscore' => array (
				[
					'-one-two-._three_four_ = FOUR',
					'\A\Name\Space\Like\Key = classname',
				],
				[
					'-one-two-.' => [
						'_three_four_' => 'FOUR'
					],
					'\A\Name\Space\Like\Key' => 'classname',
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
			'multiline value' => array (
				[
					'one.two { ',
					'	three (',
					'         multiline 1',
					'         multiline 2',
					'	)',
					'	empty (',
					'	)',
					'} ',
				],
				[
					'one.' => [
						'two.' => [
							'three' =>
							'         multiline 1' . "\n"
							.  '         multiline 2',
							'empty' => '',
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
			'copying relative' => array (
				[
					'one {',
					'  two = 2',
					'  two.three = 3',
					'  four < .two',
					'  two = 22',
					'  two.three = 33',
					'} ',
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
					'one { ',
					'   value = value',
					'   value := prependString(pre_)',
					'} ',
				],
				[
					'one.' => [
						'two' => 'FIVE',
						'two.' => [
							'three' => 'FIVE'
						],
						'value' => 'pre_value',
					]
				],
			),
		];
	}

	static public function getConditionsExamples() {
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
					'condition.' => ['2' => '2'],
					'after' => '99',
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
					'condition.' => ['1' => '1'],
					'after' => '99',
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
					'condition.' => [
						'1' => '1',
						'2' => '2'
					],
					'after' => '99',
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
					'condition.' => [
						'2' => '2'
					],
					'after' => '99',
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
					'condition.' => [
						'1' => '1'
					],
					'after' => '99',
				],
			),
			'complex'  => array (
				[
					'[TRUE]',
						'/ single slash comment',
						'// double slash comment',
						'# hash comment',
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
