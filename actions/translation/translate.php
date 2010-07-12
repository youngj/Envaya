<?php

    gatekeeper();
    action_gatekeeper();

    $text = get_input('translation');
    $guid = get_input('entity_guid');
    $isHTML = (int)get_input('html');
    $property = get_input('property');
    $entity = get_entity($guid);

    if (!$entity->canEdit())
    {
        register_error(elgg_echo("org:cantedit"));
        forward_to_referrer();
    }
    else if (empty($text))
    {
        register_error(elgg_echo("trans:empty"));
        forward_to_referrer();
    }
    else
    {
        $origLang = $entity->getLanguage();

        $actualOrigLang = get_input('language');
        $newLang = get_input('newLang');

        if ($actualOrigLang != $origLang)
        {
            $entity->language = $actualOrigLang;
            $entity->save();
        }
        if ($actualOrigLang != $newLang)
        {
            $trans = lookup_translation($entity, $property, $actualOrigLang, $newLang, TranslateMode::ManualOnly, $isHTML);
            if (!$trans)
            {
                $trans = new Translation();
                $trans->container_guid = $entity->guid;
                $trans->property = $property;
                $trans->lang = $newLang;
            }
            $trans->html = $isHTML;
            $trans->owner_guid = get_loggedin_userid();
            $trans->value = $text;
            $trans->save();
        }

        system_message(elgg_echo("trans:posted"));

        forward(get_input('from') ?: $entity->getUrl());
    }
?>
