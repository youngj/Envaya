<?php
    $org = $vars['org'];
    $email = $vars['email'];
    $lang = $org->language;

    $imageDir = Config::get('url')."_graphics/email";
?>
<html>
<head>
<?php
    if (@$vars['base'])
    {
        echo "<base href='{$vars['base']}' />";
    }
?>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor='#FFFFFF' style="font-size:13px;color:#000000;line-height:150%;font-family:trebuchet ms;">
<?php echo $email->render_content($org); ?>
<br />
<div style="font-size:10px;color:#996600;line-height:100%;font-family:verdana;">

<?php echo sprintf(
    __('email:about',$lang),
    escape($org->email ?: "{{email}}"),
    escape($org->name ?: "{{name}}")
); ?>

<br />
<br />
<?php echo sprintf(
    __('email:change',$lang),
    "<a target='_blank' href='".Config::get('url')."pg/email_settings?e=".urlencode($org->email)."&c=".get_email_fingerprint($org->email)."'>".
    __('email:here',$lang)."</a>"
);
?>
<br />
<br />
<?php echo __('email:envaya',$lang); ?>
<br />
<?php echo __('email:mailing_address',$lang); ?>
<br />
</div>

</body>
</html>