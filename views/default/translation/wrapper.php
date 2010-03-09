<?php

$translation = $vars['translation'];
$obj = $vars['entity'];
$prop = $vars['property'];

$text = ($translation) ? $translation->value : $obj->$prop;

if (isadminloggedin())
{
    echo "<a class='transContributeLink' href='{$CONFIG->url}pg/org/translate/{$obj->guid}/".escape($prop)."'>".elgg_echo("trans:contribute")."</a>";
}

echo elgg_view("output/longtext",array('value' => $text));
