<?php

    $org = $vars['entity'];  

    if ($vars['full']) 
    {            
        $aboutContent = view_translated($org, 'mission');
        
        $sectors = $org->getSectors();
        
        if (!empty($sectors))
        {        
            $sectorOptions = Organization::getSectorOptions();
            $sectorNames = array();

            foreach ($sectors as $sector)
            {
                $sectorNames[] = "<a href='org/search?sector=$sector'>".escape($sectorOptions[$sector])."</a>";
            }

            $sectorText = elgg_echo("org:sectors") . ": " . implode(', ', $sectorNames);               
            $aboutContent .= "<div class='org_website'>$sectorText</div>";
        }    
                        
        $title = elgg_echo("org:mission");
                
        echo elgg_view_layout('section', $title, $aboutContent);
        
        $posts = $org->listNewsUpdates(5, false);
        
        if (!$posts)
        {
            $posts = elgg_echo("org:noupdates");
        }
        else
        {
            $posts .= "<a class='float_right' href='".$org->getUrl()."/news'>View all updates</a>";
        }
                
        echo elgg_view_layout('section', elgg_echo("org:updates"), $posts);        

        $entityLat = $vars['entity']->getLatitude();
        if (!empty($entityLat)) 
        { 
                        
            echo elgg_view_layout('section', elgg_echo("org:map"),                 
                
                elgg_view("org/map", array(
                    'lat' => $entityLat, 
                    'long' => $vars['entity']->getLongitude(),
                    'zoom' => 10,
                    'pin' => true,
                    'static' => true
                ))
            );        
        }    

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
    
    }    



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
