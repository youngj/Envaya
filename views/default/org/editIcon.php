    <div class='section_header'><?php echo elgg_echo('org:icon'); ?></div>
    <div class='section_content padded'>
    
        <?php
        
            $user = page_owner_entity();
        
            echo elgg_view("input/image", 
                array(
                    'current' => $user->getIcon('medium'),
                    'removable' => $user->custom_icon,
                    'internalname' => 'icon',
                    'deletename' => 'deleteicon',
                )) 
        
         ?> 