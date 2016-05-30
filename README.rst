=================================
Next Generation TypoScript Parser
=================================

:state: alpha
:license: GPLv2

This composer package is an extension for the TYPO3 CMS. The goal is to provide
a family of parsers as alternatives for the current TypoScript parser of the
core. Moreover the following goals are in focus:

* coding style
* flexibility
* performance

The package may be used standalone outside of the TYPO3 CMS as well. The
dependency from the TYPO3 CMS in incapsulated into an adapter class.

Manual: http://typoscript-parser.readthedocs.io/en/latest/index.html

Getting Started as T3 Extension
===============================

It's a TYPO3 CMS extension. Install it like an extension.

Manual: http://typoscript-parser.readthedocs.io/en/latest/index.html

Standalone Testing
==================

::

    git clone https://github.com/elmar-hinz/TX.tsp.git tsp
    cd tsp
    composer install
    ./phake
    ./phake test:unit
    ./phake test:dox

