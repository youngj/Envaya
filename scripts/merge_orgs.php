<?php

/*
 * Merges data from two organizations into one, for cases when
 * people accidentally created multiple accounts for the same 
 * organization.
 */

require_once "scripts/cmdline.php";
require_once "start.php";

Config::set('debug', false);

$opts = getopt('h');

if (sizeof($argv) != 3 || isset($opts['h']))
{
    echo "Usage: {$argv[0]} <username1> <username2>\n";    
    echo "Disables username2 and merges their data into username1\n";
    die;
}

function get_org($username)
{
    $org = Organization::query()->where('username = ?', $username)->get();

    if (!$org)
    {
        echo "Invalid username: {$username}\n";
        die;
    }
    
    return $org;
}

$dest_org = get_org($argv[1]);
$dest_guid = $dest_org->guid;

$src_org = get_org($argv[2]);
$src_guid = $src_org->guid;
    
echo "Destination org: {$dest_org->name} ({$dest_org->username}) guid=$dest_guid\n";
echo "Source org:      {$src_org->name} ({$src_org->username}) guid=$src_guid\n";

if (prompt_default("OK? [y/n]", "") != 'y')
{
    exit;
}

function update_guids($models, $props, $src_guid, $dest_guid)
{
    foreach ($models as $model)
    {
        foreach ($props as $prop)
        {        
            if ($model->$prop == $src_guid)
            {
                $model->$prop = $dest_guid;
            }
        }
        try
        {
            $model->save();    
        }
        catch (DatabaseException $ex)
        {
            echo "Could not save ".get_class($model).".\n";
        }
    }
}

// migrate child widgets of Home page
$src_widget = $src_org->get_widget_by_class('Home');
$dest_widget = $dest_org->get_widget_by_class('Home');
if ($src_widget->guid && $dest_widget->guid)
{
    $src_sections = $src_widget->query_widgets();
    foreach ($src_sections as $src_section)
    {
        $dest_section = $dest_widget->get_widget_by_class($src_section->subclass);
        if (!$dest_section || !$dest_section->guid)
        {
            $src_section->container_guid = $dest_widget->guid;
            $src_section->save();
        }
    }
}

// migrate child widgets of News page
$src_widget = $src_org->get_widget_by_class('News');
$dest_widget = $dest_org->get_widget_by_class('News');
if ($src_widget->guid && $dest_widget->guid)
{
    update_guids(
        $src_widget->query_widgets()->filter(),
        array('container_guid'),
        $src_widget->guid,
        $dest_widget->guid
    );
}

// migrate duplicate pages, but with a unique widget name to avoid collisions
$widgets = $src_org->query_widgets()->show_disabled(true)->filter();
foreach ($widgets as $widget)
{
    if ($dest_org->get_widget_by_name($widget->widget_name)->guid)
    {
        $widget->widget_name = "{$widget->widget_name}_old{$widget->guid}";
        $widget->container_guid = $dest_guid;
        $widget->disable();
        $widget->save();
    }
}

// migrate feed_names
$old_feed_name = FeedItem::make_feed_name(array('user' => $src_guid));
$new_feed_name = FeedItem::make_feed_name(array('user' => $dest_guid));
foreach (FeedItem::query()->where('feed_name = ?', $old_feed_name)->filter() as $feed_item)
{
    $feed_item->feed_name = $new_feed_name;
    $feed_item->save();
}

// migrate all properties pointing to the source organization's guid to the destination organization's guid
$classes = array_merge(array(
        'EntityMetadata',
        'FeedItem',
        'Translation',
        'OrgPhoneNumber',
        'OrgDomainName'
    ),
    EntityRegistry::all_classes()
);

foreach ($classes as $cls)
{
    $attributes = $cls::get_table_attributes();
    $guid_columns = array();
    $guid_values = array();
    
    foreach ($attributes as $column_name => $default)
    {
        if (preg_match('/guid$/', $column_name))
        {
            $guid_columns[] = $column_name;
            $guid_values[] = $src_guid;
        }
    }
    
    if (sizeof($guid_columns))
    {
        $where = implode(' OR ', array_map(function($n) { return "`$n` = ?"; }, $guid_columns));    
        
        $query = $cls::query()->where($where)->args($guid_values);
        
        if (method_exists($query, 'show_disabled'))
        {
            $query->show_disabled(true);
        }
        
        echo $query->get_filter_sql();
        echo "\n";
                
        update_guids(
            $query->filter(),
            $guid_columns,
            $src_guid,
            $dest_guid
        );
    }
}

$redirect = NotFoundRedirect::new_simple_redirect("/{$src_org->username}","/{$dest_org->username}");
$redirect->save();

echo "added 404 redirect: $redirect\n";

$src_org->username = $src_org->username . ".deleted";
$src_org->approval = -1;
$src_org->disable();
$src_org->save();

echo "done!\n";