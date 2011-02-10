<?php

function get_storage()
{
    global $CONFIG;
	$storage_backend = $CONFIG->storage_backend;
	return new $storage_backend();
}

function get_scribd()
{
    require_once "vendors/scribd.php";
    global $CONFIG;
    return new Scribd($CONFIG->scribd_key, $CONFIG->scribd_private);    
}
