<?php

$answer = time() * 2;

echo "<b id='captcha_answer'>$answer</b>";
echo view('input/hidden', array('name' => 'captcha_answer', 'value' => $answer));

?>
