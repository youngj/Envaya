<div class='section_content padded'>
<?php
$report = $vars['report'];
$start = $vars['start'];
$scroll_position = $vars['scroll_position'];
?>
<form method='POST' action='<?php echo $report->get_url()."/save" ?>'>
<?php
echo view('input/securitytoken'); 
echo $report->render_edit();
?>
</form>
</div>

<?php
if ($scroll_position)
{
?>
<script type='text/javascript'>
setTimeout(function() {
    window.scrollTo(0, <?php echo (int)$scroll_position ?>);
}, 1);
</script>
<?php
}
?>

