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
        'defaults' => array('controller' => 'Translate')
    ));
});