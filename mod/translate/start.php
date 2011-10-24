<?php

Engine::add_autoload_action('EntityRegistry', function() {
    EntityRegistry::register_subtypes(array(
        'translate.lang' => 'TranslationLanguage',
        'translate.key' => 'TranslationKey',
        'translate.translation' => 'Translation',
        'translate.vote' => 'TranslationVote',
        'translate.translator.stats' => 'TranslatorStats',
        'translate.comment' => 'TranslationKeyComment',
        'translate.entity.key' => 'EntityTranslationKey',
        'translate.interface.group' => 'InterfaceGroup',
        'translate.interface.key' => 'InterfaceKey',
    ));
});

Engine::add_autoload_action('Controller_Default', function() {
    Controller_Default::add_route(array(
        'regex' => '/tr\b',
        'controller' => 'Controller_Translate',
    ));
});

Engine::add_autoload_action('PageContext', function() {
    PageContext::add_mixin_class('Mixin_TranslateContext');
});

Engine::add_autoload_action('Language', function() {
    Language::add_fallback_group('itrans', 'itrans_admin');
});

Engine::add_autoload_action('Mixable', function() {
    Mixable::extend_mixin_class('Mixin_Content', 'Mixin_TranslatableContent');
});


if (@Config::get('translate:footer_link'))
{
    Views::extend('page_elements/footer', 'page_elements/translate_footer');
}

Views::extend('page_elements/content_wrapper', 'page_elements/translate_bar', -1);
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