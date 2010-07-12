<?php

    require_once("scripts/cmdline.php");
    require_once("engine/start.php");

    $newsUpdates = NewsUpdate::filterByCondition(
        array('data_types = ?'),
        array(DataType::Image),
        '',
        20
    );

    $s3 = get_s3();

    function copyImage($newsUpdate, $size)
    {
        $oldFile = $newsUpdate->getImageFile($size);
        $groupName = $newsUpdate->guid;

        if ($oldFile->exists())
        {
            $newFile = new ElggFile();
            $newFile->owner_guid = $newsUpdate->owner_guid;
            $newFile->group_name = $groupName;
            $newFile->size = $size;
            $newFile->setFilename("$size.jpg");
            $oldFile->copyTo($newFile);
            $newFile->save();

            return $newFile;
        }
    }

    foreach ($newsUpdates as $newsUpdate)
    {
        echo "{$newsUpdate->getURL()}:\n";

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

        $htmlContent = $newsUpdate->renderContent();
        $class = $htmlContent ? "" : " class='last-paragraph'";
        $htmlContent = "<p$class><img class='image_center' src=\"{$large->getURL()}\" /></p>{$htmlContent}";

        $newsUpdate->setContent($htmlContent, true);
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
        echo "{$widget->getURL()}:\n";

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

        $htmlContent = $widget->renderContent();

        $imagePos = $widget->image_position;

        $class = $htmlContent ? "" : " class='last-paragraph'";

        if ($imagePos == 'left' || $imagePos == 'right')
        {
            $htmlContent = "<img class='image_$imagePos' src=\"{$medium->getURL()}\" />{$htmlContent}";
        }
        else if ($imagePos == 'top')
        {
            $htmlContent = "<p$class><img class='image_center' src=\"{$large->getURL()}\" /></p>{$htmlContent}";
        }
        else if ($imagePos == 'bottom')
        {
            $htmlContent = "{$htmlContent}<p class='last-paragraph'><img class='image_center' src=\"{$large->getURL()}\" /></p>";
        }

        $widget->setContent($htmlContent, true);
        $widget->save();
    }