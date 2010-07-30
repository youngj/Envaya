<?php
    global $CONFIG;

    $org = $vars['org'];
    $lang = $org->language;

    $imageDir = "{$CONFIG->url}_graphics/email";
?>
<html>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor='#FFFFFF' style="font-size:13px;color:#000000;line-height:150%;font-family:trebuchet ms;">

<p>
<?php echo sprintf(__('email:salutation', $lang), escape($org->name)) ?>
<br />
<br />
<?php echo __('email:greetings',$lang) ?>
<br />
<br />
<?php echo __('email:reminder:while',$lang). " " ?>
<?php echo sprintf(__('email:reminder:addnews',$lang),
    "<a target='_blank' href='{$CONFIG->url}pg/login?username={$org->username}'>".__('email:clickhere',$lang)."</a>"
);
?>
<br />
<br />
<?php echo sprintf(__('email:reminder:formatting',$lang),
    "<a target='_blank' href='{$org->getURL()}/projects/edit?username={$org->username}'>".__('email:reminder:projectspage',$lang)."</a>"
);
?>
<br />
<br />
<?php echo sprintf(__('email:reminder:feedback',$lang),
    "<a href='mailto:{$CONFIG->admin_email}'>{$CONFIG->admin_email}</a>"
);
?>
<br />
<br />
<?php echo __('email:closing',$lang) ?>
<br />
<?php echo __('email:from',$lang) ?>
</p>
<br />
<div style="font-size:10px;color:#996600;line-height:100%;font-family:verdana;">

<?php echo sprintf(
    __('email:about',$lang),
    escape($org->email),
    escape($org->name)
); ?>

<br />
<br />
<?php echo sprintf(
    __('email:change',$lang),
    "<a target='_blank' href='{$CONFIG->url}org/emailSettings?e=".urlencode($org->email)."&c=".get_email_fingerprint($org->email)."'>".
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