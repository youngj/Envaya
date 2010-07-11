<?php

    gatekeeper();
    action_gatekeeper();

    $guid = (int) get_input('blogpost');
    $body = get_input('blogbody');
    $blog = get_entity($guid);

    $imageFiles = get_uploaded_files($_POST['image']);

    if ($blog->getSubtype() != T_blog || !$blog->canEdit())
    {
        register_error(elgg_echo("org:cantedit"));
        forward_to_referrer();
    }
    else if (empty($body) && !$imageFiles && !$blog->hasImage())
    {
        register_error(elgg_echo("blog:blank"));
        forward_to_referrer();
    }
    else if (get_input('delete'))
    {
        $org = $blog->getContainerEntity();
        $blog->disable();
        $blog->save();
        system_message(elgg_echo('blog:delete:success'));
        forward($org->getURL()."/news");
    }
    else
    {
        $blog->access_id = ACCESS_PUBLIC;
        $blog->content = sanitize_html($body);
        $blog->setDataType(DataType::HTML, true);
        $blog->save();

        if (get_input('deleteimage'))
        {
            $blog->setImages(null);
        }
        else if ($imageFiles)
        {
            $blog->setImages($imageFiles);
        }

        system_message(elgg_echo("blog:updated"));
        forward($blog->getUrl());
    }

?>
