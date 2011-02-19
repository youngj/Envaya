<?php

/*
 * Creates a local config file for the current computer, where default settings can be overridden.
 * This file should not be committed to source control.
 */

$settings_file = "config/local.php";

if (!is_file($settings_file))
{
    copy("scripts/settings_template.php", $settings_file);
    echo "Created $settings_file with default settings.\n";
}
