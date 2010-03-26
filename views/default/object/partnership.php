<?php
  
    $entity = $vars['entity'];
    $url = $entity->getURL();    

?>
   
<div class="partnership_view">    
    <?php 
        $partnerOrgEntity = $entity->getPartner();
    
        echo "<img style='float:left;' src='" . $partnerOrgEntity->getIcon('tiny') . "' />";
        echo "<a href='{$partnerOrgEntity->getUrl()}'>{$partnerOrgEntity->name}</a>";
        echo "<div style='clear:both;' />";
        echo view_translated($entity, 'description'); 
        echo "<br><br>";

    ?>              
    <div style='clear:both'></div>
</div>
