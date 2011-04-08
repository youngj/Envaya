<?php

$answer = time() * 2;

echo "<b id='recaptcha_answer'>$answer</b>";
echo view('input/hidden', array('name' => 'recaptcha_challenge_field', 'value' => $answer));
echo view('input/text', array('name' => 'recaptcha_response_field'));

?>
