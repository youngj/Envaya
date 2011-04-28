<?php

// Prompt user at command line to create admin user account

require_once("scripts/cmdline.php");
require_once("engine/start.php");

$username = prompt_default("Admin username", "testadmin");
$password = prompt_default("Admin password", "testtest");
$name = prompt_default("Admin name", "Test Admin");
$email = prompt_default("Admin email", '');

$user = new User();
$user->username = $username;
$user->set_password($password);
$user->name = $name;
$user->email = $email;
$user->admin = true;    
$user->save();
echo "Admin created\n";

Config::set('debug', false);