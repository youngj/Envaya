<?php

Engine::add_autoload_action('EntityRegistry', function() {
    EntityRegistry::register_subtype('translate.interface.lang', 'InterfaceLanguage');
    EntityRegistry::register_subtype('translate.interface.group', 'InterfaceGroup');
    EntityRegistry::register_subtype('translate.interface.key', 'InterfaceKey');
    EntityRegistry::register_subtype('translate.interface.trans', 'InterfaceTranslation');
    EntityRegistry::register_subtype('translate.interface.key.comment', 'InterfaceKeyComment');
    EntityRegistry::register_subtype('translate.vote', 'TranslationVote');
    EntityRegistry::register_subtype('translate.translator.stats', 'TranslatorStats');
});

Engine::add_autoload_action('Controller_Default', function() {
    Controller_Default::add_route(array(
        'regex' => '/tr\b',
        'controller' => 'Controller_Translate',
    ));
});

Engine::add_autoload_action('Language', function() {
    Language::add_fallback_group('itrans', 'itrans_admin');
});

if (@Config::get('translate:footer_link'))
{
    Views::extend('page_elements/content_footer', 'page_elements/translate_footer');
}


if (@Config::get('translate:live_interface'))
{
    Engine::add_autoload_action('Language', function() {
        $language = Language::current();
    
        $interface_language = InterfaceLanguage::get_by_code($language->get_code());
        
        if ($interface_language)
        {   
            $language->load_all();
        
            $interface_keys = $interface_language->query_keys()->where("best_translation <> ''")->filter();
            
            $translations = array();
            foreach ($interface_keys as $interface_key)
            {
                $translations[$interface_key->name] = $interface_key->best_translation;
            }
                        
            $language->add_translations($translations);
        }
    });
}