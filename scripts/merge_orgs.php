<?php

require_once "scripts/cmdline.php";
require_once "engine/start.php";

//global $CONFIG;
//$CONFIG->debug = false;

$opts = getopt('h');

if (sizeof($argv) != 3 || isset($opts['h']))
{
    echo "Usage: {$argv[0]} <username1> <username2>\n";    
    echo "Disables username2 and merges their data into username1\n";
    die;
}

function get_org($username)
{
    $org = Organization::query(true)->where('username = ?', $username)->get();

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

$update_guids = function($models, $props) use ($src_guid, $dest_guid)
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
};   

$widgets = Widget::query()->where('container_guid = ?', $src_guid)->show_disabled(true)->filter();
foreach ($widgets as $widget)
{
    if ($dest_org->get_widget_by_name($widget->widget_name)->guid)
    {
        $widget->handler_class = $widget->get_handler_class();
        $widget->widget_name = "{$widget->widget_name}_old";
        $widget->disable();
        $widget->save();
    }
}

$update_guids(
    EntityRow::query()->where('(owner_guid = ? OR container_guid = ?)', $src_guid, $src_guid)->filter(),
    array('container_guid', 'owner_guid')
);

$update_guids(
    EntityMetadata::query()->where('entity_guid = ?', $src_guid)->filter(),
    array('entity_guid')
);        

$update_guids(
    FeedItem::query()->where('(subject_guid = ? OR user_guid = ?)', $src_guid, $src_guid)->filter(),
    array('subject_guid', 'user_guid')
);

$update_guids(
    FeaturedSite::query()->where('user_guid = ?', $src_guid)->filter(),
    array('user_guid')
);

$update_guids(
    FeaturedPhoto::query()->where('user_guid = ?', $src_guid)->filter(),
    array('user_guid')
);

$update_guids(
    Translation::query()->where('(owner_guid = ? OR container_guid = ?)', $src_guid, $src_guid)->filter(),
    array('owner_guid', 'container_guid')
);  

$update_guids(
    Partnership::query()->where('partner_guid = ?', $src_guid)->filter(),
    array('partner_guid')
);  

$update_guids(
    OrgPhoneNumber::query()->where('org_guid = ?', $src_guid)->filter(),
    array('org_guid')
);

$update_guids(
    OrgDomainName::query()->where('guid = ?', $src_guid)->filter(),
    array('guid')
);    

$src_org->username = $src_org->username . ".deleted";
$src_org->disable();
$src_org->save();

echo "done!\n";