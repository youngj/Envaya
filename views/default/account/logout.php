<?php
    $next = $vars['next'];
?>
<div class='section_content padded'>
<form method='POST' action='/pg/logout'>
<?php
    $user = Session::get_logged_in_user();

    echo "<p>".sprintf(__('user:current'), escape("{$user->name} ({$user->username})"))."</p>";
    echo "<p class='last-paragraph'>".__('user:logout_instructions')."</p>";
    
    echo view('input/securitytoken');     
    echo view('input/hidden', array('name' => 'next', 'value' => $next));     
    echo view('input/submit', array('value' => __('logout')));     
?>
</form>
</div>