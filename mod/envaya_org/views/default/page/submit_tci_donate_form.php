<html>
<body onload='document.forms[0].submit()'>
Forwarding to Trust for Conservation Innovation...
<form method="POST" action="http://trustforconservationinnovation.org/donate/index.php">
<?php
    foreach ($vars as $k => $v)
    {
        echo view('input/hidden', array('name' => escape($k), 'value' => $v));
    }
?>
<input type='submit' value='Retry' />
</form>
</body>
</html>