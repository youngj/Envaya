<div class='section_content padded'>

<div class='instructions'>
<?php echo __('comment:captcha_instructions'); ?>
</div>
<form method='POST' action='<?php echo escape($_SERVER['REQUEST_URL']); ?>'>

<?php

foreach ($_POST as $k => $v)
{
	echo view('input/hidden', array('internalname' => escape($k), 'value' => $v));
}

echo view('input/hidden', array('internalname' => 'captcha', 'value' => '1'));
echo Recaptcha::get_html();

echo view('input/submit', array('value' => __('comment:submit_captcha')))	;

?>

</form>
</div>	