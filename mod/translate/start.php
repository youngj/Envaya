<?php

Engine::add_autoload_action('EntityRegistry', function() {
    EntityRegistry::register_subtype('translate.lang', 'TranslationLanguage');
    EntityRegistry::register_subtype('translate.key', 'TranslationKey');
    EntityRegistry::register_subtype('translate.translation', 'Translation');
    EntityRegistry::register_subtype('translate.vote', 'TranslationVote');
    EntityRegistry::register_subtype('translate.translator.stats', 'TranslatorStats');
    EntityRegistry::register_subtype('translate.comment', 'TranslationKeyComment');    

    EntityRegistry::register_subtype('translate.entity.key', 'EntityTranslationKey');
    EntityRegistry::register_subtype('translate.interface.group', 'InterfaceGroup');
    EntityRegistry::register_subtype('translate.interface.key', 'InterfaceKey');
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

Views::extend('css/default', 'css/default_translate');

if (@Config::get('translate:live_interface'))
{
    Engine::add_autoload_action('Language', function() {
        $language = Language::current();
    
        $translation_language = TranslationLanguage::get_by_code($language->get_code());
        
        $language->load_all();
    
        $interface_keys = $translation_language->query_keys()->where("best_translation <> ''")->filter();
        
        $translations = array();
        foreach ($interface_keys as $interface_key)
        {
            $translations[$interface_key->name] = $interface_key->best_translation;
        }
                    
        $language->add_translations($translations);
    });
}