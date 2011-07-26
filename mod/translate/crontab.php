<?php

require_once "scripts/cmdline.php";

return array(
   array(
       'interval' => 60, // minutes
       'cmd' => "php ".escapeshellarg(__DIR__."/scripts/notify_translations.php")
   ),
);