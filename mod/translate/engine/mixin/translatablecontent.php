<?php

/*
 * Extension for Mixin_Content that allows the user to translate the content using the HTML rich text editor.
 */
class Mixin_TranslatableContent extends Mixin
{
    public function get_content_translation_behavior()
    {
        return 'TranslationKeyBehavior_HTML';
    }
}