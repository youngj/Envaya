
<?php
  
    $url = $vars['entity']->getURL();
    $owner = $vars['entity']->getOwnerEntity();                    
    $canedit = $vars['entity']->canEdit();
?>

    
<div class="blog_post">    
    <?php echo elgg_view("output/longtext", array('value' => $vars['entity']->description)); ?>
      
    <?php

        if ($canedit) {

        ?>
            <a href="<?php echo $url; ?>/edit"><?php echo elgg_echo("edit"); ?></a>  &nbsp; 
            <?php

                echo elgg_view("output/confirmlink", array(
                    'href' => $vars['url'] . "action/blog/delete?blogpost=" . $vars['entity']->getGUID(),
                    'text' => elgg_echo('delete'),
                    'confirm' => elgg_echo('deleteconfirm'),
                ));
            ?>
        <?php
        }

    ?>      
      
    <p class="strapline">
        <a href="<?php echo $url; ?>"><?php echo date("M j, Y",$vars['entity']->time_created); ?></a>               
    </p>
</div>
