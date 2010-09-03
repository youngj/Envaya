<?php
$report = $vars['report'];
$submit = $vars['submit'];
?>

<div class='section_content padded report_view'>

<?php
if ($submit)  {
?>

<div class='report_preview_message'>
<p>
Carefully read your responses below and verify that they are complete and correct.
</p>
<p>
To make any changes, click "Edit Responses" next to the appropriate section.
</p>
<p class='last-paragraph'>
When you are ready to submit your report, click "Submit Report" at the bottom of the page.
</p>
</div>

<?php
}
?>


<?php
echo $report->render_view();
?>

<?php
if ($submit)  {
?>

<form method='POST' action='<?php echo $report->get_url(); ?>/submit'>
<?php echo view('input/securitytoken'); ?>

<div class='report_preview_message'>

<p>
When you are ready to submit the report, check the box below and type your full name as an electronic signature. 
</p>
<p>
After submitting the report, you will no longer be able to edit your responses.
</p>

<label><input type='checkbox' id='confirm_box' name='confirm' />I have verified that all responses are complete and correct.</label><br />

Signature:
<?php
    echo view('input/text', array('internalname' => 'signature', 'js' => 'style="width:250px"', 'internalid' => 'signature'));
?>
<br />

<script type='text/javascript'>
function verifyConfirmed()
{
    if (!document.getElementById('confirm_box').checked)
    {
        alert("Please verify that all responses are complete and correct.");
        return false;
    }
    if (!document.getElementById('signature').value)
    {
        alert("Please type your name as a signature.");
        return false;
    }
    return true;
}
</script>

<?php
    echo view('input/submit', array(
        'internalname' => '_submit',
        'value' => __('report:submit'), 
        'js' => "onclick='return verifyConfirmed() && setSubmitted()'"
    ));
?>
</div>
</form>

<?php
}
?>

</div>

