<?php

require_once("scripts/cmdline.php");
require_once("engine/start.php");

function get_feed_count($actionName, $subject)
{
	return get_data_row("SELECT COUNT(*) as total FROM feed_items WHERE subject_guid = ? AND action_name = ?",
		array($subject->guid, $actionName))->total;
}

$newsUpdates = NewsUpdate::filterByCondition(array(), array(), 'time_created desc', $limit = 100);
foreach ($newsUpdates as $newsUpdate)
{
	if (get_feed_count('news', $newsUpdate) == 0)
	{
		post_feed_items($newsUpdate->getContainerEntity(), 'news', $newsUpdate, null, $newsUpdate->time_created);
	}
}

$orgs = Organization::filterByCondition(array(), array(), 'time_created desc', $limit = 100);
foreach ($orgs as $org)
{
	if (get_feed_count('register', $org) == 0)
	{
		post_feed_items($org, 'register', $org, null, $org->time_created);
	}
}

$widgets = Widget::filterByCondition(array("widget_name='history' or widget_name='projects'"), array(), 'time_created desc', $limit = 100);
foreach ($widgets as $widget)
{
	if (get_feed_count('new_widget', $widget) == 0 && $widget->content)
	{
		post_feed_items($widget->getContainerEntity(), 'new_widget', $widget, null, $widget->time_updated);
	}
}