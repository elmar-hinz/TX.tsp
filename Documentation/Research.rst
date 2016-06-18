.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: Includes.txt

.. _research:

==============================
Research
==============================

--------------------------------------------
\\Core\\TypoScript\\Parser\\TyposcriptParser
--------------------------------------------

Overview
========

The method ``parse()`` is a preprocessor that handels including and
excluding of template parts by condtions.

It doesn't parse the incoming lines to end first, but delegates the parts
immediately to ``parseSub()`` (a kind of depth-first parsing of the template
tree).

The method ``doSyntaxHighlight()`` is responsible to generate a syntax
highlighted ``HTML`` string. It also calls the preprocessor ``parse()`` but
sets a flag, that disables the coditions, so that all parts are evaluated.

The latter is strange in two aspects. It doesn't make sense to send syntax
highlighting through a conditioning preprocessor. It doesn't make sense to
parse into an array tree, when one actually want's a ``HTML`` string as result.

Conditions
==========

In the method ``parse()`` the template is branched into rendered and
non-rendered parts based on conditions. The condition evalutation is delegated
to a ``$matchObj``, that is injected by parameter.

For each condition the method creates a hash and stores it into
``$this->sections`` array. This are used by the ``TemplateService``, to cache
the rendered templates matching combinations of conditions, that evaluate to
true.

Line numbering
==============

There is a line number offset, that sums up the line numbers of previously
rendered templates. It is advanced at end of ``parse()``.

The line numbers of the current template are tracked by ``$this->rawP`` in the
main loop of ``parseSub()`` and also for the condition sections, that evaluate
to false in the method ``nextDivider()``. ``$this->rawP`` is reset to zero at
the beginning of the rendering of the current template in  the method
``parse()``.

Error handling
==============

method ``error($errorString, $severity = 2)``.

This method collects into $this->errors[] = [a, b, c, d] with:

* a = error message
* b = severity
* c = line number
* d = template line number offset

Collected messages:

* 'Script is short of XXX braces.'
* 'An end brace is in excess.'
* 'On return to [GLOBAL] scope, the script was short of XXX braces.'
* 'A multiline value section is not ended with a parenthesis!'
* 'Object Name String, contains invalid character XXX. Must be alphanumeric or
  one of: "_:-\.".'
* 'Object Name String XXX was not followed by any operator, =<>({'
* '### ERROR: XXX' (Error to be extract from an error comment created in
  previous parsing steps like during template includes.)

Syntax highlighting
===================

Highlighted parsing is controlled by the method ``doSyntaxHighlight()``.

It sets the flag ``$this->syntaxHighLight`` to true and the template string is
parsed. The flag activates the additional highlighting functionality during the
process of parsing. Finally the method ``syntaxHighlight_print()`` is called to
format the collected results including the error messages.

Registration of highlighted parts of lines is done during parsing by the method
``regHighLight()`` if the above flag is set. The parts are collected into

* ``$this->highLightData``
* ``$this->highLightData_bracelevel``

Both arrays count per line, the first one the higlighted sections of the line,
the second one the depth of brace nesting.

Breakpoints
===========

A breakpoint is a line number in ``$this->breakPointLN`` to break the
execution of the rendering. The method ``parseSub()`` returns with a marker
``[_BREAK]``. This marker stops the further execution of the main loop
in ``parse()``.

---------------
TemplateService
---------------

``TemplateService`` is a service that makes use of the parser. A main task of
TemplateService is, to cache the rendered template for different combinations
of conditions of a page.

-----------------------
ExtendedTemplateService
-----------------------

The class ``ExtendedTemplateService`` contains method for the TS module in TYPO3
backend. It extends TemplateService.

