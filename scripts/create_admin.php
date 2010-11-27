<?php

require_once("engine/start.php");

// readline from http://us3.php.net/manual/en/function.readline.php#49937
function _readline($prompt="") {
    echo $prompt;
    $o = "";
    $c = "";
    while ($c!="\r"&&$c!="\n") {
        $o.= $c;
        $c = fread(STDIN, 1);
    }
    fgetc(STDIN);
    return $o;
}

function prompt_default($prompt, $default)
{
    return _readline("$prompt [$default]") ?: $default;
}

$username = prompt_default("Admin username", "testadmin");
$password = prompt_default("Admin password", "testtest");
$name = prompt_default("Admin name", "Test Admin");
$email = prompt_default("Admin email", '');

$new_user = register_user($username, $password, $name, $email, true);
$new_user->admin = true;    
$new_user->save();
echo "Admin created\n";

global $CONFIG;
$CONFIG->debug = false;