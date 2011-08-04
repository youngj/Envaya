<?php

/*
 * Behavior for a translation key that represents HTML content.
 */
class TranslationKeyBehavior_HTML extends TranslationKeyBehavior
{
    public function sanitize_value($value)
    {
        return Markup::sanitize_html($value);
    }
    
    public function view_value($value, $snippet_len = null)
    {
        if ($snippet_len != null)
        {
            $value = preg_replace('/<img [^>]+>/', ' <strong>(image)</strong> ', $value);
            $value = preg_replace('/<scribd [^>]+>/', ' <strong>(document)</strong> ', $value);
        
            return Markup::snippetize_html($value, $snippet_len, array(
                'HTML.AllowedElements' => 'br',
                'AutoFormat.Linkify' => false,
                'AutoFormat.RemoveEmpty' => true
            ));
        }
        else
        {
            echo view('translate/html_value', array(
                'value' => $value
            ));
        }
    }
    
    public function view_input($vars)
    {
        echo view('translate/html_input', $vars);
    }
}