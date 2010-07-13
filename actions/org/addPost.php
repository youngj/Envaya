<?php

    gatekeeper();
    action_gatekeeper();

    $body = get_input('blogbody');
    $orgId = get_input('container_guid');
    $org = get_entity($orgId);

    $imageFiles = get_uploaded_files($_POST['image']);

    if (empty($body) && !$imageFiles)
    {
        register_error(elgg_echo("blog:blank"));
        forward_to_referrer();
    }
    else if (!$org->canEdit())
    {
        register_error(elgg_echo("org:cantedit"));
        forward_to_referrer();
    }
    else
    {
        $uuid = get_input('uuid');

        $duplicates = get_entities_from_metadata('uuid', $uuid, 'object', T_news_update, $orgId);
        if (!sizeof($duplicates))
        {
            $blog = new NewsUpdate();
            $blog->owner_guid = get_loggedin_userid();
            $blog->container_guid = $orgId;
            $blog->setContent($body, true);
            $blog->uuid = $uuid;
            $blog->save();

            system_message(elgg_echo("blog:posted"));
        }
        else
        {
            $blog = $duplicates[0];
        }

        forward($blog->getURL());
    }