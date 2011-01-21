<?php

function get_storage()
{
    global $CONFIG;
	$storage_backend = $CONFIG->storage_backend;
	return new $storage_backend();
}