<?php

    $language = $vars['language'];

    echo view('translate/key_table', array(
        'query' => EntityTranslationKey::query()
            ->where('language_guid = ?', $language->guid)
            ->order_by('time_updated desc, guid desc'),
        'language' => $language,
        'base_url' => "/tr/{$language->code}/content"
    ));
