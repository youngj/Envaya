<?php
  
    $entity = $vars['entity'];
    $url = $entity->getURL();    

?>
   
<div class="team_member_view">       
    <?php if ($entity->hasImage()) { ?>    
    <div class='team_member_img'><img src='<?php echo $entity->getImageURL('small') ?>' /></div>
    <?php } ?>
    <div class='team_member_content'>
        <div class='team_member_name'><?php echo escape($entity->name); ?></div>    
        <?php 
            echo elgg_view('output/longtext', array('value' => translate_field($entity, 'description'))); 
        ?>
    </div>
<div style='clear:both;'></div>        
</div>
