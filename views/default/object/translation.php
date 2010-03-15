<?php

$trans = $vars['entity'];

$by = elgg_echo("trans:by");

echo "$by ";

if ($trans->owner_guid == 0)
{
    echo "<a href='http://translate.google.com'>Google Translate</a>";
}
else
{
    $owner = $trans->getOwnerEntity();
    $url = $owner->getURL();
    echo "<a href='$url'>".escape($owner->name)."</a>";
}

?>