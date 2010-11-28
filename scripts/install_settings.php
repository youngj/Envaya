<?php

$settings_file = "engine/settings.php";

if (!is_file($settings_file))
{
    copy("scripts/settings_template.php", $settings_file);
    echo "Created $settings_file with default settings.\n";
}
