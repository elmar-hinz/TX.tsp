===============================
Non-recursive TypoScript Parser
===============================

:state: experimental
:license: GPLv2

This composer package is a extension for the TYPO3 CMS. The goal is to provide
a family of parsers as alternatives for the current TypoScript parser of the
core. Moreover the following goals are in focus:

* performance
* coding style
* flexibility

The package may be used standalone outside of the TYPO3 CMS as well. The
dependency from the TYPO3 CMS in incapsulated into an adapter class.

What is the difference to the original TypoScript parser?
=========================================================

The original TypoScript parser uses recursive function calls to parse the
data within nested braces. This parser is just one single loop. It makes
use of a plain old stack to track the nesting.

Getting started
===============

::

    git clone https://github.com/elmar-hinz/TX.tsp.git tsp
    cd tsp
    composer install
    ./phake
    ./phake test:unit
    ./phake test:dox


