<?php

/*
 * Behavior for a translation key that represents text from Envaya's interface.
 */
class TranslationKeyBehavior_InterfaceText extends TranslationKeyBehavior
{
    public function sanitize_value($value)
    {
        // don't allow people to sneak in bad HTML into translations
        $value = Markup::sanitize_html($value, array(
            'AutoFormat.Linkify' => false,
        ));                
        
        $placeholders = Language::get_placeholders($value);
        $correct_placeholders = $this->get_placeholders();
        sort($correct_placeholders);
        sort($placeholders);
        if ($correct_placeholders != $placeholders)
        {
            throw new ValidationException(__('itrans:placeholder_error'));
        }
        
        return $value;
    }    
    
    
    public function view_input($vars)
    {    
        return parent::view_input($vars) . 
            view('translate/interface_placeholders', array(
                'placeholders' => $this->get_placeholders()
            ));
    }
}