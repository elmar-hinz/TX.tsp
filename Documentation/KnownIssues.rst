.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: Includes.txt

.. _issues:

============
Known Issues
============

No Exceptions are Thrown
========================

The TypoScript production parser currently doesn't throw execptions. It
expects valid TS as input. The syntax higlighting parser is designed to
inspect TS for mistakes.

The original parser doesn't throw exceptions either. Modules of the backend are
not prepared to catch exeptions from the parser and break if execeptions
would be thrown from insane TS.

Intolerant for Insane TS
========================

The TypoScript production parser will silently break, if feed with insane TS.
It is optimized for speed and is less tolerant for insane TS than the origional
parser.

This means in rare cases code that works for the original parser may break with
the TypoScript production parser. Use the syntax highlighting parser to fix the
TS code.



