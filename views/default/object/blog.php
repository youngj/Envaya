<?php
  
    $entity = $vars['entity'];
    $url = $entity->getURL();    
    $full = $vars['full'];
    
    $nextUrl = $url . "/next";
    $prevUrl = $url . "/prev";        
?>
  
<div class="blog_post">    
    <?php 

        if ($entity->hasImage())
        {
            $imageSize = ($full) ? 'large' : 'small';        
            $imgLink = ($full) ? $nextUrl : $url;
            echo "<a class='{$imageSize}BlogImageLink' href='$imgLink'><img src='{$entity->getImageURL($imageSize)}' /></a>";            
        }
        
        echo view_translated($entity, 'content'); 
        
        echo "<span class='blog_date'>{$entity->getDateText()}</span>";
    ?>              
    <div style='clear:both'></div>
</div>