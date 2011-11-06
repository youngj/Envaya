<?php
    $next = $vars['next'];
?>
<div class='section_content padded'>
<form method='POST' action='/pg/logout'>
<p>
<?php
    echo __('user:logout_instructions');
?>
</p>
<?php     
    echo view('input/securitytoken');     
    echo view('input/hidden', array('name' => 'next', 'value' => $next));     
    echo view('input/submit', array('value' => __('logout')));     
?>
</form>
</div>