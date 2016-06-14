<?php

namespace ElmarHinz\TypoScriptParser\Interfaces;

interface TypoScriptParsetimeExceptionInterface
{

    public function getTemplateLineNumber();

    public function isEndOfTemplateException();
}


