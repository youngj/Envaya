<?php
    global $CONFIG;
    $user = page_owner_entity();
    
    if ($user && $user instanceof Organization) 
    {
?>
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

    <h3><?php echo elgg_echo('org:theme'); ?></h3>
    <p>
    
    <?php
        
        $curTheme = $user->theme;        
        
        foreach (get_themes() as $theme)
        {
            $selected = ($theme == $curTheme) ? "checked='checked'" : '';
            $label = elgg_echo("theme:$theme");
            echo "<label class='optionLabel'><input type='radio' name='theme' value='".escape($theme)."' {$selected} class='input-radio' />{$label}</label><br />";
        }
    ?>

    </p>

<?php } ?>