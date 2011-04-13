<?php
    
    $org = $vars['org'];
    $orgUrl = $org->get_url();
?>
<a class='feed_org_icon' href='<?php echo $orgUrl ?>'><?php 
    echo view('org/icon', array('org' => $org, 'size' => '40x60')); 
?></a>