<?php

/*
 * Creates a local config file for the current computer, where default settings can be overridden.
 * This file should not be committed to source control.
 */

$settings_file = "config/local.php";

if (!is_file($settings_file))
{
    $settings_template = file_get_contents("scripts/settings_template.php");
    
    $settings = strtr($settings_template, array(
        '{{site_secret}}' => md5(rand().microtime())
    ));

    file_put_contents($settings_file, $settings);
    echo "Created $settings_file with default settings.\n";
}
