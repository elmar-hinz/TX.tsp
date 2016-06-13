<?php

namespace ElmarHinz\TypoScriptParser\Interfaces;

interface TypoScriptValueModifierInterface
{

	/**
	 * Modify the input string by the given operation.
	 *
	 * The modifier oparation is an unparsed string, i.e.:
	 *   'removeFromList(2,1)'
	 *
	 * It depends fully apon the implementation, which formats are supported.
	 *
	 * @param string The input value.
	 * @param string The operation.
	 * @return string The modified input value.
	 */
    public function modifyValue($value, $unparsedModifierOperation);
}

