<?php
    
    $org = $vars['org'];
    $orgIcon = $org->get_icon('small');
    $orgUrl = $org->get_url();
?>
<a class='feed_org_icon' href='<?php echo $orgUrl ?>'><img src='<?php echo $orgIcon ?>' /></a>