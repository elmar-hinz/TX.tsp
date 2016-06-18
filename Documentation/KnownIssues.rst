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

The TypoScript production parser currently doesn't throw execptions. It expects
valid TS as input. To check if your input is valid use the syntax higlighting
parser in the BE.

No exceptions are thrown because the original parser doesn't throw exceptions
either. Modules of the backend are not prepared to catch exeptions from the
parser and break if execeptions would be thrown from invalid TS.

Intolerant for Invalid TS
=========================

The TypoScript production parser will silently break, if feed with invalid TS.
It is optimized for speed and is less tolerant for invalid TS than the
origional parser.

This means in rare cases code that works for the original parser may break with
the TypoScript production parser. Use the syntax highlighting parser to fix the
TS code.

XCLASS issues
=============

The origional parser is not fully replaced but extended by XCLASS registration.
The extended class serves as adapter to the standalone classes. Conflicts may
occur with extensions, that also XCLASS the core parser.


