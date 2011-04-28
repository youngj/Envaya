<?php

EntityRegistry::register_subtype('translate.interface.lang', 'InterfaceLanguage');
EntityRegistry::register_subtype('translate.interface.group', 'InterfaceGroup');
EntityRegistry::register_subtype('translate.interface.key', 'InterfaceKey');
EntityRegistry::register_subtype('translate.interface.trans', 'InterfaceTranslation');
EntityRegistry::register_subtype('translate.vote', 'TranslationVote');
EntityRegistry::register_subtype('translate.translator.stats', 'TranslatorStats');

Controller_Default::add_route(array(
    'regex' => '/tr\b',
    'defaults' => array('controller' => 'Translate')
));
