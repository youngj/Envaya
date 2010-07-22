<?php

$entity = $vars['entity'];
$property = $vars['property'];
$isHTML = $vars['isHTML'];

$org = $entity->getRootContainerEntity();

$text = $entity->get($property);

$height = 300;

ob_start();

echo "<table class='gridView' style='width:1100px;margin:0 auto'><tr><td  style='width:50%;padding-right:10px'>";
echo "<h3>".sprintf(elgg_echo("trans:original_in"),
     elgg_view('input/language', array(
        'internalname' => 'language',
        'value' => $entity->getLanguage()
    ))
).": </h3>";

if ($isHTML)
{
    $leftHeight = $height - 30;
    echo "<div style='height:{$leftHeight}px;border:1px solid black;padding:5px;margin-top:25px;overflow:auto'>";
    echo sanitize_html($text);
    echo "</div>";
}
else
{
    echo elgg_view("output/longtext", array('value' => $text));
}
echo "</td><td style='width:50%'>";

$lang = get_language();
$langStr = elgg_echo($lang, $lang);

$curTranslation = lookup_translation($entity, $property, $entity->getLanguage(), $lang, TranslateMode::All, $isHTML);

$curText = ($curTranslation) ? $curTranslation->value : $text;

$transIn = sprintf(elgg_echo("trans:inlang"), elgg_view('input/language', array(
        'internalname' => 'newLang',
        'value' => $lang
    ))
);

echo "<h3>$transIn: </h3>";

if ($isHTML)
{
    echo elgg_view("input/tinymce", array(
        'internalname' => 'translation',
        'height' => $height,
        'value' => $curText));
}
else
{
    echo elgg_view("input/longtext", array('internalname' => 'translation', 'value' => $curText));
}

echo "<br>".
    elgg_view("input/hidden", array('internalname' => 'entity_guid', 'value' => $entity->guid)).
    elgg_view("input/hidden", array('internalname' => 'property', 'value' => $property)).
    elgg_view("input/hidden", array('internalname' => 'html', 'value' => $isHTML)).
    elgg_view("input/hidden", array('internalname' => 'from', 'value' => $vars['from'])).
    elgg_view('input/submit', array('internalname' => 'submit', 'trackDirty' => true, 'value' => elgg_echo('trans:submit')));

echo "</td></tr></table>";

$formBody = ob_get_clean();

echo elgg_view('input/form', array('action' => "org/save_translation", 'body' => $formBody));