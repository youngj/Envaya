<?php

$lang = $vars['lang'];
$key = $vars['key'];

$from = get_input('from');

$trans = InterfaceTranslation::getByKeyAndLang($key, $lang);

echo "<form method='POST' action='org/save_interface_item'>";

echo view('input/securitytoken');

echo view('input/hidden', array('internalname' => 'from', 'value' => $from));
echo view('input/hidden', array('internalname' => 'key', 'value' => $key));

$enText = __($key, 'en');

echo "<div class='input'>";
echo "<label>".sprintf(__("trans:original_in"), escape(__('en'))).":</label>";
echo "<div>".view('output/longtext', array('value' => $enText))."</div>";
echo "</div>";

if ($trans)
{
echo "<div class='input'>";
echo "<label>".sprintf(__("trans:previous_in"), escape(__($lang))).":</label>";
echo "<div>".view('output/longtext', array('value' => __($key, $lang)))."</div>";
echo "</div>";

}

echo "<div class='input'>";
echo "<label>".sprintf(__("trans:inlang"), escape(__($lang))).":</label>";

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

global $CONFIG;
$value = $trans ? $trans->value : $CONFIG->translations[$lang][$key];
if (!$value)
{
    $value = GoogleTranslate::get_auto_translation($enText, 'en', $lang);
}

echo view($input, array('internalname' => 'value', 'value' => $value, 'js'=>$js));

echo view('input/submit', array('value' => __('save')));

echo "<a style='float:right;padding:10px' href='".escape($from)."'>".__('cancel')."</a>";

echo "</div>";

echo "</form>";