<?php

require_once "scripts/cmdline.php";
require_once "start.php";

$max_disk_pct = Config::get('max_disk_pct');

$res = `df`;
$lines = explode("\n", $res);

class LowDiskSpaceException extends Exception {}

foreach ($lines as $line)
{
    if (preg_match('#(?P<pct>\d+)%#', $line, $match))
    {
        if ((int)$match['pct'] > $max_disk_pct)
        {
            throw new LowDiskSpaceException($line);
        }
    }
}
