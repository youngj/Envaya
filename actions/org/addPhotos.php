<?php

    gatekeeper();
    action_gatekeeper();

    $org_guid = (int)get_input('org_guid');
    $org = get_entity($org_guid);

    if ($org && $org->canEdit())
    {
        $imageNumbers = get_input_array('imageNumber');

        $uuid = get_input('uuid');

        $duplicates = get_entities_from_metadata('uuid', $uuid, 'object', T_news_update, $org_guid);
		if (!sizeof($duplicates))
		{
			foreach ($imageNumbers as $imageNumber)
			{
				$imageFiles = get_uploaded_files(get_input("imageData$imageNumber"));
				$imageCaption = get_input("imageCaption$imageNumber");

				if ($imageFiles)
				{
					$blog = new NewsUpdate();
					$blog->owner_guid = get_loggedin_userid();
					$blog->container_guid = $org_guid;
					$blog->content = $imageCaption;
					$blog->uuid = $uuid;
					$blog->save();

					$blog->setImages($imageFiles);
				}
			}
		}
        forward($org->getUrl() . "/news");
    }