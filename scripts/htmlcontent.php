<?php

    require_once("scripts/cmdline.php");
    require_once("engine/start.php");

    $newsUpdates = NewsUpdate::filterByCondition(
        array('data_types = ?'),
        array(DataType::Image),
        '',
        100
    );

    $s3 = get_s3();

    function copyImage($newsUpdate, $size)
    {
        $oldFile = $newsUpdate->getImageFile($size);
        $groupName = $newsUpdate->guid;

        if ($oldFile->exists())
        {
            $newFile = new UploadedFile();
            $newFile->owner_guid = $newsUpdate->owner_guid;
            $newFile->group_name = $groupName;
            $newFile->size = $size;
            $newFile->filename = "$size.jpg";
            $oldFile->copy_to($newFile);
            $newFile->save();

            return $newFile;
        }
    }

    foreach ($newsUpdates as $newsUpdate)
    {
        echo "{$newsUpdate->get_url()}:\n";

        $small = copyImage($newsUpdate, 'small');
        if (!$small)
        {
            continue;
        }
        $large = copyImage($newsUpdate, 'large');
        if (!$large)
        {
            continue;
        }

        echo " converting to html\n";

        $htmlContent = view('output/longtext', array('value' => $newsUpdate->content));
        $class = $htmlContent ? "" : " class='last-paragraph'";
        $htmlContent = "<p$class><img class='image_center' src=\"{$large->get_url()}\" /></p>{$htmlContent}";

        $newsUpdate->set_content($htmlContent, true);
        $newsUpdate->save();
    }

    $widgets = Widget::filterByCondition(
        array('data_types = ?', "(widget_name = 'history' or widget_name = 'projects')"),
        array(DataType::Image),
        '',
        20
    );

    foreach ($widgets as $widget)
    {
        echo "{$widget->get_url()}:\n";

        $small = copyImage($widget, 'small');
        if (!$small)
        {
            continue;
        }
        $medium = copyImage($widget, 'medium');
        if (!$medium)
        {
            continue;
        }
        $large = copyImage($widget, 'large');
        if (!$large)
        {
            continue;
        }

        echo " converting to html\n";

        $htmlContent = view('output/longtext', array('value' => $widget->content));

        $imagePos = $widget->image_position;

        $class = $htmlContent ? "" : " class='last-paragraph'";

        if ($imagePos == 'left' || $imagePos == 'right')
        {
            $htmlContent = "<img class='image_$imagePos' src=\"{$medium->get_url()}\" />{$htmlContent}";
        }
        else if ($imagePos == 'top')
        {
            $htmlContent = "<p$class><img class='image_center' src=\"{$large->get_url()}\" /></p>{$htmlContent}";
        }
        else if ($imagePos == 'bottom')
        {
            $htmlContent = "{$htmlContent}<p class='last-paragraph'><img class='image_center' src=\"{$large->get_url()}\" /></p>";
        }

        $widget->set_content($htmlContent, true);
        $widget->save();
    }


    $newsUpdates = NewsUpdate::filterByCondition(
        array('data_types = 0', "content <> ''"),
        array(),
        '',
        100
    );

    foreach ($newsUpdates as $newsUpdate)
    {
        echo "{$newsUpdate->get_url()}:\n";
        echo " converting to html\n";
        $htmlContent = view('output/longtext', array('value' => $newsUpdate->content));
        $newsUpdate->setContent($htmlContent, true);
        $newsUpdate->save();
    }

    $widgets = Widget::filterByCondition(
        array('data_types = 0', "content <> ''", "(widget_name = 'history' or widget_name = 'projects' or widget_name = 'home')"),
        array(),
        '',
        200
    );

    foreach ($widgets as $widget)
    {
        echo "{$widget->get_url()}:\n";
        echo " converting to html\n";
        $htmlContent = view('output/longtext', array('value' => $widget->content));
        $widget->set_content($htmlContent, true);
        $widget->save();
    }

    $translations = Translation::filterByCondition(
        array('html=0', "property='content'"),
        array(),
        '',
        100
    );

    foreach ($translations as $translation)
    {
        //echo "$translation->container_guid\n";
        $container = $translation->get_container_entity();
        if ($container)
        {
            echo "{$translation->get_container_entity()->get_url()}\n";
            $translation->value = view('output/longtext', array('value' => $translation->value));
            $translation->html = 1;
            $translation->save();
        }
    }