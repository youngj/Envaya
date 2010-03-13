<?php

    $org = $vars['entity'];  
    
    $widget = $org->getWidgetByName('home');
    echo $widget->renderView();
    
    /*
    

    $contactContent = '';
    if ($org->email_public != 'no')
    {
        $contactContent .= "<div class='org_website'>". elgg_echo('org:email') . ": " . elgg_view("output/email",array('value' => $org->email))."</div>";   
    }        
    if ($org->website)
    {
        $contactContent .= "<div class='org_website'>". elgg_echo('org:website') . ": " . elgg_view("output/url",array('value' => $org->website))."</div>";   
    }        
    if ($contactContent)
    {
        echo elgg_view_layout('section', elgg_echo("org:contact"), $contactContent);    
    }    
    */


	if($vars['entity'] && isadminloggedin())
	{
	    if (!$vars['entity']->isApproved())
    	{
?>
    <div class="contentWrapper">
    <div>
    	<form action="<?php echo $vars['url'] . "action/org/approve"; ?>" method="post">
    	    <?php echo elgg_view('input/securitytoken'); ?>
    		<?php
    			$warning = elgg_echo("org:approveconfirm");
    			?>
    			<input type="hidden" name="org_guid" value="<?php echo $vars['entity']->getGUID(); ?>" />
    			<input type="hidden" name="user_guid" value="<?php echo page_owner_entity()->guid; ?>" />
    			<input type="submit" class="submit_button" name="approve" value="<?php echo elgg_echo('org:approve'); ?>" onclick="javascript:return confirm('<?php echo $warning; ?>')"/>
    	</form>
    </div><div class="clearfloat"></div>
    
<?php
        }
	}
	

?>
