<?php
    $next = @$vars['next'];
?>
<p>
<?php echo sprintf(__('login:if_not_registered'), "<a href='/org/new' style='font-weight:bold'>".__('sign_up_now')."</a>"); ?>
</p>
<p>
<?php echo "<a href='/pg/register?next=".urlencode($next)."'>".__('login:register_link')."</a>"; ?>
</p>
