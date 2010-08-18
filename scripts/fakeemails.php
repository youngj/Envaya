<?php

require_once("scripts/cmdline.php");
require_once("engine/start.php");

$users = User::all('', 1000);

foreach ($users as $user)
{
    echo "{$user->email}\n";
    $user->email = "adunar+{$user->username}@gmail.com";
    $user->save();
}
