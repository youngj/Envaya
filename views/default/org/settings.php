<?php
    global $CONFIG;
    $user = page_owner_entity();
    
    if ($user && $user instanceof Organization) 
    {
?>

    <h3>    
        <?php echo elgg_echo('theme') ?>
    </h3>
    <p>
    <?php 
        echo elgg_echo('theme:current');
        echo " ";
        echo escape(elgg_echo("theme:{$user->theme}"));
    ?>    
    <strong><a href='<?php echo $user->getURL() ?>/theme?from=pg/settings/user/<?php echo $user->username ?>'><?php echo elgg_echo('theme:edit') ?></a></strong>
    </p>
    <p>
    
    </p>

    <h3><?php echo elgg_echo('org:icon'); ?></h3>
    <p>
    
        <?php
        
            echo elgg_view("input/image", 
                array(
                    'current' => $user->getIcon('medium'),
                    'removable' => $user->custom_icon,
                    'internalname' => 'icon',
                    'deletename' => 'deleteicon',
                )) 
        
         ?> 

    </p>



<?php } ?>