<?php

    gatekeeper();
    action_gatekeeper();

    $org_guid = get_input('org_guid');
    $org = get_entity($org_guid);
    if ($org && $org->canEdit())
    {
        $name = get_input('widget_name');
        $widget = $org->getWidgetByName($name);

        if (get_input('delete'))
        {
            $widget->disable();
            $widget->save();

            system_message(elgg_echo('widget:delete:success'));
            forward($org->getURL());
        }
        else
        {
            if (!$widget->isEnabled())
            {
                $widget->enable();
            }

            try
            {
                $widget->saveInput();

                system_message(elgg_echo('widget:save:success'));
                forward($widget->getURL());
            }
            catch (Exception $ex)
            {
                action_error($ex->getMessage());
            }
        }

    }
    register_error(elgg_echo("org:cantedit"));
    forward();
?>