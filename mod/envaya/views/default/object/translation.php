<?php

$text = $vars['entity']->text;

$by = elgg_echo("translation_by");

echo "<div class='transSource'>$by ".$vars['entity']->getSource().": </div>";

echo elgg_view("output/longtext",array('value' => $text));

?>