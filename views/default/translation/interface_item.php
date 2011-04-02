<?php

$lang = $vars['lang'];
$key = $vars['key'];

$from = get_input('from');

$trans = InterfaceTranslation::get_by_key_and_lang($key, $lang);

echo "<form method='POST' action='/tr/save_interface_item'>";

echo view('input/securitytoken');

echo view('input/hidden', array('name' => 'from', 'value' => $from));
echo view('input/hidden', array('name' => 'key', 'value' => $key));

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

$value = $trans ? $trans->value : Language::get($lang)->get_translation($key);
if (!$value)
{
    $value = GoogleTranslate::get_auto_translation($enText, 'en', $lang);
}

echo view($input, array('name' => 'value', 'value' => $value, 'js'=>$js));

echo view('input/submit', array('value' => __('save')));

echo "<a style='float:right;padding:10px' href='".escape($from)."'>".__('cancel')."</a>";

echo "</div>";

echo "</form>";