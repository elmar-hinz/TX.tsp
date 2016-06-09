<?php

namespace ElmarHinz\TypoScriptParser;

/* use \ElmarHinz\TypoScriptParser\Exceptions\TypoScriptParsetimeException; */

class TypoScriptTokenTracker
{

    protected $tokens = [];

	/**
	 * Push a token.
	 *
	 * The token classes are defined as constants in AbstractTypoScriptParser.
	 *
     * @see TypoScriptFormatterInterface::pushToken()
	 * @param integer $lineNumber the line number.
	 * @param integer $tokenClass The token class.
	 * @param string $string The token string.
	 * @return void
	 */
	public function push($lineNumber, $tokenClass, $token)
	{
        $this->tokens[$lineNumber][]
            = array( 'class' => $tokenClass, 'value' => $token);
	}

    public function getByLine($lineNumber)
    {
        if(array_key_exists($lineNumber, $this->tokens)) {
            return $this->tokens[$lineNumber];
        } else {
            return [];
        }
    }

}

