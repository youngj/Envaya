<?php
  
    $entity = $vars['entity'];
    $url = $entity->getURL();    
    $partner = $entity->getPartner();

?>
   
<div class="partnership_view">    
    <a class='feed_org_icon' href='<?php echo $partner->getURL() ?>'><img src='<?php echo $partner->getIcon('small') ?>' /></a>
    <div class='feed_content'>
    <a class='feed_org_name' href='<?php echo $partner->getUrl() ?>'><?php echo escape($partner->name); ?></a><?php echo (($entity->description) ? ":" : ""); ?>
    <span><?php    
        echo view('output/longtext', array('value' => translate_field($entity, 'description'))); 
    ?></span>
    </div>
<div style='clear:both;'></div>        
</div>
