<?php
    $report = $vars['report'];
?>

<html>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" bgcolor='#FFFFFF' style="font-size:13px;color:#000000;line-height:150%;font-family:trebuchet ms;">

<p>
<?php echo __('report:copy_included'); ?>
</p>

<?php
    echo $report->render_view();
?>

</body>
</html>