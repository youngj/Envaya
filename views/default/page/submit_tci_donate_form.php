<html>
<body onload='document.donate_form.submit()'>
Forwarding to Trust for Conservation Innovation...
<form id='donate_form' name='donate_form' method="POST" action="https://secure16.inmotionhosting.com/~trustf5/payment.php">
<?php
    foreach ($vars as $k => $v)
    {
        echo view('input/hidden', array('name' => escape($k), 'value' => $v));
    }
?>
<input type='submit' name='_submit' value='Retry' />
</form>
</body>
</html>