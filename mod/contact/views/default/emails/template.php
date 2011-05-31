<?php
    $user = $vars['user'];
    
    if (!$user)
    {
        $user = new User();        
        $user->name = "{{name}}";
        $user->username = "{{username}}";
        $user->email = "{{email}}";
    }
    
    $email = $vars['email'];
    $lang = $user->language;
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
<?php echo $email->render_content($user); ?>
<br />
<div style="font-size:10px;color:#996600;line-height:100%;font-family:verdana;">

<?php 
    echo strtr(__('contact:about_email',$lang), array(
        '{email}' => escape($user->email), 
        '{name}' => escape($user->name)
    ));
?>

<br />
<br />
<?php 
echo sprintf(
    __('contact:unsubscribe',$lang),
    "<a target='_blank' href='{$user->get_email_settings_url()}'>".__('here',$lang)."</a>"
);
?>
<br />
<br />
<?php echo Config::get('contact:email_footer_html'); ?>
<br />
</div>

</body>
</html> 