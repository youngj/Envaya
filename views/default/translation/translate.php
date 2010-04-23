<?php

$entity = $vars['entity'];
$property = $vars['property'];

$org = $entity->getRootContainerEntity();

$text = $entity->get($property);

echo "<h3>".sprintf(elgg_echo("trans:original_in"), elgg_echo($org->language)).": </h3>";
echo elgg_view("output/longtext", array('value' => $text));

$lang = get_language();
$langStr = elgg_echo($lang, $lang);

$curTranslation = lookup_translation($entity, $property, $org->language, $lang);

$curText = ($curTranslation) ? $curTranslation->value : $text;

$transIn = sprintf(elgg_echo("trans:inlang"), $langStr);

echo "<h3>$transIn: </h3>";
$formBody = elgg_view("input/longtext", array('internalname' => 'translation', 'value' => $curText)).
            elgg_view("input/hidden", array('internalname' => 'entity_guid', 'value' => $entity->guid)).
            elgg_view("input/hidden", array('internalname' => 'property', 'value' => $property)).
            elgg_view("input/hidden", array('internalname' => 'from', 'value' => $vars['from'])).
            elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('trans:submit')));

echo elgg_view('input/form', array('action' => "{$vars['url']}action/translate", 'body' => $formBody));