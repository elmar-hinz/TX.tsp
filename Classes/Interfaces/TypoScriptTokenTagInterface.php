<?php

namespace ElmarHinz\TypoScriptParser\Interfaces;

interface TypoScriptTokenTagInterface
{

    public function setTag($tag);
    public function getTag();
    public function setClasses($classes);
    public function getClasses();
    public function toTag();

}



