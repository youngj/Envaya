<div class='section_content padded'>

<div class='instructions'>
<?php echo $vars['instructions']; ?>
</div>
<form method='POST' action='<?php echo escape($_SERVER['REQUEST_URL']); ?>'>

<?php

$fields = array();
foreach ($_POST as $k => $v)
{
    if (strpos($k, 'captcha') === false)
    {
        $fields[$k] = $v;
    }
}

echo view('input/hidden_multi', array('fields' => $fields));

echo view('input/hidden', array('name' => 'captcha', 'value' => '1'));
echo Captcha::get_html();
echo view('input/text', array('name' => 'captcha_response'));

echo view('input/submit', array('value' => __('captcha:button')));

?>

</form>
</div>	