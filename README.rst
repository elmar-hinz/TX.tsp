==============================
Nonrecursive TypoScript Parser
==============================

:state: experimental

What is the difference to the original TypoScript parser?
=========================================================

The original TypoScript parser uses recursive function calls to parse the
data within nested braces. This parser is just one single loop. It makes
use of a plain old stack to track the nesting.

The goal is to measure the difference of speed.

Memory allocation
=================

Both parsers need to allocate new memory for every node of the parse
tree. I guess that is the critical point. I follow to different strategies
to push the memory allocation to the C level of PHP.

Getting started
===============

::

    git clone https://github.com/elmar-hinz/TYPO3.TypoScriptParser.git Test
    cd Test/
    composer install
    ./phake test:all


