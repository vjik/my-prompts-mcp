---
name: detailed-argument
title: Text Translator
description: Translate text to a specified language
arguments:
  - name: text
    description: The text to translate
    required: true
  - name: language
    description: Target language (e.g. French, Spanish, German)
    required: true
---
Translate the following text to {{language}}:

{{text}}

Provide only the translated text without any additional commentary.
