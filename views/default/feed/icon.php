<?php    
    $user = $vars['user'];
?>
<a class='feed_org_icon' href='<?php echo $user->get_url(); ?>'><?php 
    echo view('account/icon', array('user' => $user, 'size' => '40x60')); 
?></a>