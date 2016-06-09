.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: Includes.txt

.. _tokens:

======
Tokens
======

The Token Hierarchy
===================

   * AbstractTypoScriptToken
      * TypoScriptIgnoredToken
      * TypoScriptOperatorToken
      * TypoScriptValueToken
      * TypoScriptCommentContextToken
      * TypoScriptKeysPostspaceToken
      * TypoScriptPrespaceToken
      * TypoScriptCommentToken
      * TypoScriptKeysToken
      * TypoScriptValueContextToken
      * TypoScriptConditionToken
      * TypoScriptOperatorPostspaceToken
      * TypoScriptValueCopyToken

Tokens as Type
==============

First of all the token object is a device to ship a type and a value. The Type
is the class itself, the value is set with the constructor and accessible by
the method ``getValue()``.

Tokens to Format Token Tags
===========================

The token object represents a token type, not a formatting class. Despite of
this, by calling the method ``toTag()`` a HTML tag representation of the token
can be created. This is just additional sugar in addition to the primary
function.  String representations of the token can be created by external
methods as well.  The tag creation can be customized by the methodes
``setTag()`` and ``setClasses()``.  The default values are chosen to match the
CSS classes of the existing syntax highlighting of the backend.




