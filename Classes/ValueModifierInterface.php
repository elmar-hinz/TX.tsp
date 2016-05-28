<?php

namespace ElmarHinz\TypoScript;

interface ValueModifierInterface
{
	/**
	 * Modify the input string
	 *
	 * @param string The input value.
	 * @param string The modifier name.
	 * @param string The modifier argument.
	 * @return string The modified input value.
	 */
    public function modifyValue($value, $modifier, $argument = null) : string;
}

