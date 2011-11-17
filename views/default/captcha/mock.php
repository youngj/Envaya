<?php

$answer = generate_random_code(8);

echo "<b id='captcha_answer'>$answer</b>";
echo view('input/hidden', array('name' => 'captcha_answer', 'value' => $answer));

?>
