<?php

$dir = dirname(__DIR__);
 
require_once "$dir/scripts/cmdline.php";
require_once "$dir/start.php";

$root = UserScope::get_root();
if (!$root)
{
    $root = new UserScope();
    $root->save();
}