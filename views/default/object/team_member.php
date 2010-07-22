<?php

    $entity = $vars['entity'];

?>

<?php if ($entity->hasImage()) { ?>
    <img src='<?php echo $entity->getImageURL('small') ?>' class='image_right' />
<?php } ?>
    <h3><?php echo escape($entity->name); ?></h3>
<p>
<?php
    echo elgg_view('output/longtext', array('value' => $entity->description));
?>
</p>
<div style='clear:both;'></div>
