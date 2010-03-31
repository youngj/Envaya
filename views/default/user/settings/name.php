<?php
	/**
	 * Provide a way of setting your full name.
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */

	$user = page_owner_entity();
	
	if ($user) {
?>
	<div class='section_header'><?php echo elgg_echo('user:settings'); ?></div>
    <div class='section_content padded'>
	<div class='input'>
		<label><?php echo elgg_echo('user:name:label'); ?>:</label><br />
		<?php

			echo elgg_view('input/text',array('internalname' => 'name', 'trackDirty' => true, 'value' => $user->name));
			echo elgg_view('input/hidden',array('internalname' => 'guid', 'value' => $user->guid));
		?> 
	</div>
    
    <div class='input'>
        <label><?php echo elgg_echo('user:password:label'); ?>:</label><br />
        <?php
            echo elgg_view('input/password',array('internalname' => 'password', 'trackDirty' => true));
        ?>
        <div class='help'><?php echo elgg_echo('user:password:help'); ?></div>
    </div>
    <div class='input'>
        <label>
        <?php echo elgg_echo('user:password2:label'); ?>:</label><br /> <?php
            echo elgg_view('input/password',array('internalname' => 'password2', 'trackDirty' => true));
        ?>
    </div>
    
    <div class='input'>
        <label><?php echo elgg_echo('email:address:label'); ?>:</label><br />
        <?php

            echo elgg_view('input/email',array('internalname' => 'email', 'value' => $user->email));
        
        ?> 
    </div>    
    
    <div class='input'>
    
        <label><?php echo elgg_echo('user:language:label'); ?>:</label><br />
        <?php
            $value = $CONFIG->language;
            if ($user->language)
                $value = $user->language;
            
            echo elgg_view("input/pulldown", array('internalname' => 'language', 'value' => $value, 'options_values' => get_installed_translations(true)));
        
         ?> 

    </div>   
    
    <?php echo elgg_view('input/submit', array('value' => elgg_echo('savechanges'), 'trackDirty' => true)); ?>
    
    </div>
        
    
<?php } ?>