<div class='section_content padded'>
<form method='POST' action='/admin/subscriptions'>
<?php
    echo view('input/securitytoken');
?>
<p>Enter the email address whose subscriptions you want to view/edit:</p>

<?php
    echo view('input/text', array('name' => 'email'));
    echo view('focus', array('name' => 'email'));
    
    echo "<br />";
    echo view('input/submit', array('value' => __('submit')));
?>

</form>
</div>