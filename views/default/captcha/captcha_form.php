<div class='section_content padded'>

<div class='instructions'>
<?php echo $vars['instructions']; ?>
</div>
<form method='POST' action='<?php echo escape($_SERVER['REQUEST_URL']); ?>'>
<?php

$fields = array();
foreach ($_POST as $k => $v)
{
    if (strpos($k, 'captcha') === false && $k != '__token' && $k != '__ts')
    {
        $fields[$k] = $v;
    }
}

echo view('input/securitytoken');
echo view('input/hidden_multi', array('fields' => $fields));

echo view('input/hidden', array('name' => 'captcha', 'value' => '1'));
echo Captcha::get_html();

// don't want restore_input functionality from input/text view...
echo '<input type="text" name="captcha_response" value="" class="input-text"/>';

echo view('input/submit', array('value' => __('captcha:button')));
echo view('focus', array('name' => 'captcha_response'));

?>

</form>
</div>	