<?php

$entity = $vars['entity'];
$property = $vars['property'];
$isHTML = $vars['isHTML'];

$org = $entity->getRootContainerEntity();

$text = $entity->get($property);

$height = 300;

ob_start();

echo "<table class='gridView' style='width:1100px;margin:0 auto'><tr><td  style='width:50%;padding-right:10px'>";
echo "<h3>".sprintf(__("trans:original_in"),
     view('input/language', array(
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
    echo view("output/longtext", array('value' => $text));
}
echo "</td><td style='width:50%'>";

$lang = get_language();
$langStr = __($lang, $lang);

$curTranslation = $entity->lookup_translation($property, $entity->getLanguage(), $lang, TranslateMode::All, $isHTML);

$curText = $curTranslation->value ?: $text;

$transIn = sprintf(__("trans:inlang"), view('input/language', array(
        'internalname' => 'newLang',
        'value' => $lang
    ))
);

echo "<h3>$transIn: </h3>";

if ($isHTML)
{
    echo view("input/tinymce", array(
        'internalname' => 'translation',
        'height' => $height,
        'value' => $curText));
}
else
{    
    if (strlen($enText) > 50 || strpos($enText, "\n") !== FALSE)
    {
       $input = "input/longtext";
       $js = "style='height:".(30+floor(strlen($enText)/50)*25)."px'";
    }
    else
    {
        $input = "input/text";
        $js = '';
    }

    echo view($input, array('internalname' => 'translation', 'value' => $curText, 'js' => $js));
}

echo "<br>".
    view("input/hidden", array('internalname' => 'entity_guid', 'value' => $entity->guid)).
    view("input/hidden", array('internalname' => 'property', 'value' => $property)).
    view("input/hidden", array('internalname' => 'html', 'value' => $isHTML)).
    view("input/hidden", array('internalname' => 'from', 'value' => $vars['from'])).
    view('input/submit', array('internalname' => 'submit', 'trackDirty' => true, 'value' => __('trans:submit')));

echo "</td></tr></table>";

$formBody = ob_get_clean();

echo view('input/form', array('action' => "org/save_translation", 'body' => $formBody));