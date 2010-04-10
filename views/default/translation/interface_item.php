<?php

$lang = $vars['lang'];
$key = $vars['key'];

$from = get_input('from');

$trans = InterfaceTranslation::getByKeyAndLang($key, $lang);

echo "<form method='POST' action='action/translation/interface_item'>";

echo elgg_view('input/securitytoken');

echo elgg_view('input/hidden', array('internalname' => 'from', 'value' => $from));
echo elgg_view('input/hidden', array('internalname' => 'key', 'value' => $key));

$enText = elgg_echo($key, 'en');

echo "<div class='input'>";
echo "<label>".sprintf(elgg_echo("trans:original_in"), escape(elgg_echo('en'))).":</label>";
echo "<div>".elgg_view('output/longtext', array('value' => $enText))."</div>";
echo "</div>";

if ($trans)
{
echo "<div class='input'>";
echo "<label>".sprintf(elgg_echo("trans:previous_in"), escape(elgg_echo($lang))).":</label>";
echo "<div>".elgg_view('output/longtext', array('value' => elgg_echo($key, $lang)))."</div>";
echo "</div>";

}    

echo "<div class='input'>";
echo "<label>".sprintf(elgg_echo("trans:inlang"), escape(elgg_echo($lang))).":</label>";

if (strlen($enText) > 50)
{   
   $input = "input/longtext";
   $js = "style='height:".(30+floor(strlen($enText)/50)*25)."px'";
}
else
{
    $input = "input/text";
    $js = '';
}

echo elgg_view($input, array('internalname' => 'value', 'value' => $trans ? $trans->value : elgg_echo($key, $lang), 'js'=>$js));

echo elgg_view('input/submit', array('value' => elgg_echo('save')));

echo "<a style='float:right;padding:10px' href='".escape($from)."'>".elgg_echo('cancel')."</a>";

echo "</div>";
    
echo "</form>";  