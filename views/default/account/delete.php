<?php
    $user = $vars['user'];
    ob_start();
?>
<div class='section_content padded'>
<form method='POST' action='<?php echo $user->get_url(); ?>/delete'>
<?php 
    echo view('input/securitytoken'); 
    echo "<p>Are you sure you want to permanently delete ". escape($user->name) . " ({$user->username})? This action cannot be undone.</p>";
    
    echo "<div class='input'>";
    echo "<label>Enter your password to confirm:</label><br />";
    echo view('input/password', [
        'name' => 'password'
    ]);
    echo view('focus', ['name' => 'password']);
    echo "</div>";
    
    echo view('input/alt_submit', array(
        'name' => 'delete',
        'confirm' => 'Are you really sure?',
        'value' => "Delete {$user->username}",
    ));
?>
</form>
