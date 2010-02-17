<?php
	/**
	 * Elgg groups plugin full profile view.
	 *
	 * @package ElggGroups
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	if ($vars['full'] == true) {
		$iconsize = "large";
	} else {
		$iconsize = "medium";
	}

    $org = $vars['entity'];


    if ($vars['full'] == true) 
    {
        $aboutContent = elgg_view("output/longtext",array('value' => $org->description));
        
        if ($org->website)
        {
            $aboutContent .= "<div class='org_website'>". elgg_echo('org:website') . ": " . elgg_view("output/url",array('value' => $org->website))."</div>";   
        }        

        $title = elgg_echo("org:about");
                
        echo elgg_view_layout('section', $title, $aboutContent);

        /*
        echo elgg_view_layout('section', elgg_echo("org:logo"), 
            elgg_view("org/icon", array('entity' => $vars['entity'], 'size' => $iconsize))
        );      
        */
        
        $posts = list_user_objects($org->getGUID(),'blog',10,false);
        
        if (!$posts)
        {
            $posts = elgg_echo("org:noupdates");
        }
        else
        {
            $posts .= "<a class='float_right' href='".$org->getUrl()."blog'>View all updates</a>";
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
    
    }    


    /*        
                if($vars['entity']->isVerified())
                {
                    echo "<h4>Envaya Verified!</h4>";
                }
                                
                echo "<p>";

                $desc = $vars['entity']->description;
                
                $translation = lookup_translation($desc, $vars['entity']->language);
                
                if ($translation)
                {
                    echo elgg_view_entity($translation);
                }
                else
                {
                    echo elgg_view("output/longtext",array('value' => $desc));
                }    

                echo "</p>";

        */

	if($vars['entity'] && isadminloggedin())
	{
	    if (!$vars['entity']->isApproved())
    	{
?>
    <div class="contentWrapper">
    <div>
    	<form action="<?php echo $vars['url'] . "action/approveOrg"; ?>" method="post">
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
        else if(!$vars['entity']->isVerified())
        {
            //TODO:  we should have more than a dialog box to confirm a verification process.  we'll need a real process for this
?>
    <div class="contentWrapper">
    <div>
    	<form action="<?php echo $vars['url'] . "action/verifyOrg"; ?>" method="post">
    	    <?php echo elgg_view('input/securitytoken'); ?>
    		<?php
    			$warning = elgg_echo("org:verifyconfirm");
    			?>
    			<input type="hidden" name="org_guid" value="<?php echo $vars['entity']->getGUID(); ?>" />
    			<input type="hidden" name="user_guid" value="<?php echo page_owner_entity()->guid; ?>" />
    			<input type="submit" class="submit_button" name="approve" value="<?php echo elgg_echo('org:verify'); ?>" onclick="javascript:return confirm('<?php echo $warning; ?>')"/>
    	</form>
    </div><div class="clearfloat"></div>
    
<?php            
        }
	}
	if($vars['msg'])
	{
	    system_message(elgg_echo($vars['msg']));
	    echo "<br><b>" . $vars['msg'] . "</b>";
    }
	

?>
