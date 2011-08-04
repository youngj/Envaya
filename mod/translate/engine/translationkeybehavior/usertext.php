<?php

/*
 * Behavior for a translation key that represents user-generated text content.
 */
class TranslationKeyBehavior_UserText extends TranslationKeyBehavior
{
    public function sanitize_value($value)
    {
        return Markup::sanitize_html($value, array(
            'AutoFormat.Linkify' => false,
            'HTML.AllowedElements' => ''
        ));
    }
}