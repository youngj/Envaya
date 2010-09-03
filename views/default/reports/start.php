<?php
    $report_def = $vars['report_def'];   
    
    $username = Session::isloggedin() ? Session::get_loggedin_user()->username : null;
    
    $ts = time();
    $token = generate_security_token($ts);    
    $post_login_url = $report_def->get_url()."/new_report?__ts=$ts&__token=$token";

?>
<div class='section_content padded'>    

<p>
Report Name: <strong><?php echo escape($report_def->name); ?></strong><br />
Report Sponsor: <strong><?php echo escape($report_def->get_container_entity()->name); ?></strong>
</p>

<p>
To complete this report, provide your Envaya username and password below. If you do not have an Envaya account, 
<a href='org/new?next=<?php echo urlencode($post_login_url); ?>' style='white-space:nowrap'>click here to register</a>.
</p>

<?php echo view('account/forms/login', array('next' => $post_login_url, 'username' => $username)); ?>

</div>   
