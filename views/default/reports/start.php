<?php
    $report_def = $vars['report_def'];   
    
    $username = Session::isloggedin() ? Session::get_loggedin_user()->username : null;
    
    $ts = time();
    $token = generate_security_token($ts);    
    $post_login_url = $report_def->get_url()."/new_report?__ts=$ts&__token=$token";

?>
<div class='section_content padded'>    
<p>
<?php echo sprintf(__('report:welcome'), escape($report_def->get_container_entity()->name)); ?>
</p>

<p>
<?php echo __('report:languages'); ?>
</p>

<p>
<?php echo sprintf(__('report:editing'), escape($report_def->get_container_entity()->name))." "; ?>
<?php echo sprintf(__('report:editing_2'), "<strong>".$report_def->get_url()."/start</strong>"); ?>
</p>

<p>
<ul>

<li>
<?php echo sprintf(__('report:have_account'), "<a href='pg/login?next=".urlencode($post_login_url)."' style='white-space:nowrap;font-weight:bold'>".__('report:click_log_in')."</a>"); ?>

</li>
<li>
<?php echo sprintf(__('report:no_account'), "<a href='".$report_def->get_url()."/create_account?logout=1' style='white-space:nowrap;font-weight:bold'>".__('report:click_create')."</a>"); ?>


</li>
</p>


</div>   
