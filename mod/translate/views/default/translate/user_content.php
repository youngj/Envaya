<?php

    $language = $vars['language'];

    echo view('translate/key_table', array(
        'query' => EntityTranslationKey::query()->order_by('time_updated desc, guid desc'),
        'language' => $language,
        'base_url' => "/tr/{$language->code}/content"
    ));
