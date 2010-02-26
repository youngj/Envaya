<?php

global $CONFIG;

$translation = $vars['translation'];
$md = $vars['metadata'];


echo "<div class='transSource'>";

if ($translation)
{
    echo elgg_view_entity($translation);
}

echo ": ";

if (isloggedin())
{
    echo "<a class='transContributeLink' href='{$CONFIG->url}pg/org/translate/{$md->id}'>".elgg_echo("trans:contribute")."</a>";
}

echo "</div>";

$text = ($translation) ? $translation->text : $md->value;

echo elgg_view("output/longtext",array('value' => $text));
