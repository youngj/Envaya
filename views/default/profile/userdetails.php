<?php

	/**
	 * Elgg user display (details)
	 * 
	 * @package ElggProfile
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 * 
	 * @uses $vars['entity'] The user entity
	 */

	if ($vars['full'] == true) {
		$iconsize = "large";
	} else {
		$iconsize = "medium";
	}
	
?>

<?php	
	
	// get the user's main profile picture
	echo elgg_view(
						"profile/icon", array(
												'entity' => $vars['entity'],
												//'align' => "left",
												'size' => $iconsize,
												'override' => true,
											  )
					);


?>
		
	<?php 
	
	// Simple XFN
	$rel = "";
	if (page_owner() == $vars['entity']->guid)
		$rel = 'me';

	//insert a view that can be extended
	echo elgg_view("profile/status", array("entity" => $vars['entity']));
	
		if ($vars['full'] == true) {
	
	?>
	<?php
		$even_odd = null;
		
		if (is_array($vars['config']->profile) && sizeof($vars['config']->profile) > 0)
			foreach($vars['config']->profile as $shortname => $valtype) {
				if ($shortname != "description") {
					$value = $vars['entity']->$shortname;
					if (!empty($value)) {
					
				//This function controls the alternating class
                $even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';					
	

	?>
	<p class="<?php echo $even_odd; ?>">
		<b><?php

			echo __("profile:{$shortname}");
		
		?>: </b>
		<?php

			echo elgg_view("output/{$valtype}",array('value' => $vars['entity']->$shortname));
		
		?>
		
	</p>

	<?php
					}
				}
			}
			
		}
	
	?>

	<p class="profile_aboutme_title"><b><?php echo __("profile:aboutme"); ?></b></p>
	
	<?php if ($vars['entity']->isBanned()) { ?>
		<div id="profile_banned">	
		<?php 
		    echo __('profile:banned'); 
		?>
		</div><!-- /#profile_info_column_right -->
	
	
<?php } ?>



