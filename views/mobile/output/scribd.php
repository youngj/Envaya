<?php
    $docid = $vars['docid'];
    $accesskey = $vars['accesskey'];
    $filename = $vars['filename'];
?>
<a href='http://www.scribd.com/full/<?php echo (int)$docid; ?>?access_key=<?php echo escape($accesskey); ?>'><?php echo escape($filename); ?></a>