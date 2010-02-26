
<?php
  
    $entity = $vars['entity'];
    $url = $entity->getURL();
    $canedit = $entity->canEdit();
?>

    
<div class="blog_post">    
    <?php 
        
        $imageSize = ($vars['full']) ? 'large' : 'small';
        $imageFile = $entity->getImageFile($imageSize);
        if ($imageFile->exists())
        {
            echo "<a class='{$imageSize}BlogImageLink' href='$url'><img src='$url/image/$imageSize?{$entity->time_updated}' /></a>";            
        }
        
        echo view_translated($entity, 'content'); 

        if ($canedit) {

        ?>
            <div class="blogEditControls">
            <a href="<?php echo $url; ?>/edit"><?php echo elgg_echo("edit"); ?></a>  &nbsp; 
            <?php

                echo elgg_view("output/confirmlink", array(
                    'href' => $vars['url'] . "action/news/delete?blogpost=" . $entity->getGUID(),
                    'text' => elgg_echo('delete'),
                    'confirm' => elgg_echo('deleteconfirm'),
                ));
            ?>
            </div>
        <?php
        }

    ?>      
      
    <p class="strapline">
        <?php echo date("M j, Y",$vars['entity']->time_created); ?>
    </p>
</div>
