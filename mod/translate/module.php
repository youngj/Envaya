<?php

class Module_Translate extends Module
{
    static $autoload_patch = array(
        'ClassRegistry',      
        'Controller_Default',
        'PageContext',
        'Language',
        'Mixable',
        'Hook_ViewDashboard',
        'Hook_RenderEntityProperty',
    );

    static $view_patch = array(
        'page_elements/footer',
        'page_elements/content_wrapper',
        'css/default'
    );

    static function patch_view_page_elements_footer(&$views)
    {
        $views[] = 'page_elements/translate_footer';
    }

    static function patch_view_page_elements_content_wrapper(&$views)
    {
        array_unshift($views, 'page_elements/translate_bar');
    }
    
    static function patch_view_css_default(&$views)
    {
        $views[] = 'css/default_translate';
    }

    static function patch_ClassRegistry()
    {
        ClassRegistry::register(array(
            'translate.lang' => 'TranslationLanguage',
            'translate.key' => 'TranslationKey',
            'translate.translation' => 'Translation',
            'translate.vote' => 'TranslationVote',
            'translate.translator.stats' => 'TranslatorStats',
            'translate.comment' => 'TranslationKeyComment',
            'translate.entity.key' => 'EntityTranslationKey',
            'translate.interface.group' => 'InterfaceGroup',
            'translate.interface.key' => 'InterfaceKey',
            
            'translate.permission.edittranslation' => 'Permission_EditTranslation',
            'translate.permission.managelanguage' => 'Permission_ManageLanguage',
        ));
    }

    static function patch_Controller_Default()
    {
        Controller_Default::add_route(array(
            'regex' => '/tr\b',
            'controller' => 'Controller_Translate',
        ));
    }

    static function patch_PageContext()
    {
        PageContext::add_mixin_class('Mixin_TranslateContext');
    }

    static function patch_Language()
    {
        Language::add_fallback_group('itrans', 'itrans_admin');

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
    }

    static function patch_Mixable()
    {
        Mixable::extend_mixin_class('Mixin_Content', 'Mixin_TranslatableContent');        
    }

    static function patch_Hook_ViewDashboard()
    {
        Hook_ViewDashboard::register_handler('Handler_TranslateViewDashboard');
    }

    static function patch_Hook_RenderEntityProperty()
    {
        Hook_RenderEntityProperty::register_handler('Handler_TranslateEntityField');
    }   
}
