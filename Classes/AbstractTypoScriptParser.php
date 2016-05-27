<?php

namespace ElmarHinz;

abstract class AbstractTypoScriptParser
{
	/**
	 * The lines to parse.
	 */
	protected $inputLines = Null;

	const COMMENT = '/^\//';
	const VOID = '/^\s*$/';
	const PATH = '/^\s*([[:alnum:].]*[[:alnum:]])\s*([=<{)])\s*(.*)$/';
	const CLOSE = '/^\s*}/';

	/**
	 * Join multiple templates before parsing them.
	 *
	 * The template may be a multiline text
	 * or a text that is alreay split into lines.
	 *
	 * @param mixed Multiline text or array of lines.
	 */
	public function appendTemplate($template)
	{
		if (!is_array($template)) {
			if(substr($template, -1) == "\n")
			   $template = substr($template, 0, -1);
			$template = explode("\n", $template);
		}
		if($this->inputLines == Null) {
			$this->inputLines = $template;
		} else {
			foreach ($template as $line)  $this->inputLines[] = $line;
		}
	}

	/**
	 * Parse the input
	 *
	 * Depending on the type of the parser the return value may be the final
	 * TypoScript tree array or an intermediate state.
	 *
	 * @return array The parsed result.
	 */
	public abstract function parse();

}

