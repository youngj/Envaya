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

?>

<div id="groups_info_column_right"><!-- start of groups_info_column_right -->
    <div id="groups_icon_wrapper"><!-- start of groups_icon_wrapper -->

        <?php
		    echo elgg_view(
					"org/icon", array(
												'entity' => $vars['entity'],
												//'align' => "left",
												'size' => $iconsize,
											  )
					);
        ?>

    </div><!-- end of groups_icon_wrapper -->
</div><!-- end of groups_info_column_right -->

<div id="groups_info_column_left"><!-- start of groups_info_column_left -->
    <?php
        if ($vars['full'] == true) {
                if($vars['entity']->isVerified())
                {
                    echo "<h4>Envaya Verified!</h4>";
                }

		        foreach($vars['config']->org_fields as $shortname => $valtype) {
			        if ($shortname != "name") {
				        $value = $vars['entity']->$shortname;

					    if (!empty($value)) {
					        //This function controls the alternating class
                		    $even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';
					    }

					    echo "<p class=\"{$even_odd}\">";
						echo "<b>";
						echo elgg_echo("org:{$shortname}");
						echo ": </b>";

						echo elgg_view("output/{$valtype}",array('value' => $vars['entity']->$shortname));

						echo "</p>";
				    }
				}

                echo "<p><b>";
                echo elgg_echo("org:map");
                echo ": </b>";
                $entityLat = $vars['entity']->getLatitude();
                if (!empty($entityLat)) 
                { 
                    echo elgg_view("org/map", array(
                        'lat' => $entityLat, 
                        'long' => $vars['entity']->getLongitude(),
                        'zoom' => 10,
                        'width' => 300,
                        'pin' => true,
                        'height' => 200,
                        'static' => true
                    ));                                  
                }
                echo "</p>";
		}
	?>
</div><!-- end of groups_info_column_left -->

<div id="groups_info_wide">

	<p class="groups_info_edit_buttons">

<?php
	if ($vars['entity']->canEdit())
	{

?>

		<a href="<?php echo $vars['url']; ?>mod/envaya/editOrg.php?group_guid=<?php echo $vars['entity']->getGUID(); ?>"><?php echo elgg_echo("edit"); ?></a>


<?php

	}
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

	</p>
</div>
<div class="clearfloat"></div>