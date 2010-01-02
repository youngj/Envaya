<?php
 
    // pages on the group index page

    //check to make sure this group forum has been activated
    if($vars['entity']->pages_enable != 'no'){

?>

<div id="group_pages_widget">
<h2><?php echo elgg_echo("pages:groupprofile"); ?></h2>
<?php

    $objects = list_entities("object", "page_top", page_owner(), 5, false);
	
    if($objects)
		echo $objects;
	else
		echo "<div class=\"forum_latest\">" . elgg_echo("pages:nogroup") . "</div>";
	
?>
<br class="clearfloat" />
</div>

<?php
    }
?>