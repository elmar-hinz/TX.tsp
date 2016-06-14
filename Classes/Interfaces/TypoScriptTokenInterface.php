<?php

namespace ElmarHinz\TypoScriptParser\Interfaces;

interface TypoScriptTokenInterface
{
    public function __construct($value);
    public function setValue($value);
    public function getValue();
}

