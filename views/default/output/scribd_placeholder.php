<?php
    $docid = $vars['docid'];
    $accesskey = $vars['accesskey'];
    $guid = $vars['guid'];
    $filename = $vars['filename'];
?>
<img src='/_media/images/document_icon.jpg' class='scribd_placeholder' width='100%' height='300' 
    alt='<?php echo escape($filename); ?>:<?php echo escape($docid); ?>:<?php echo escape($accesskey); ?>:<?php echo (int)($guid) ?>' />