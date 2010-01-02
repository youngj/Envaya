<?php
	/**
	 * Elgg file browser uploader
	 * 
	 * @package ElggFile
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	global $CONFIG;
	
		if (isset($vars['entity'])) {
			$title = sprintf(elgg_echo("blog:editpost"),$object->title);
			$action = "file/save";
			$title = $vars['entity']->title;
			$description = $vars['entity']->description;
			$tags = $vars['entity']->tags;
			$access_id = $vars['entity']->access_id;
		} else  {
			$title = elgg_echo("blog:addpost");
			$action = "file/upload";
			$tags = "";
			$title = "";
			$description = "";
			if (defined('ACCESS_DEFAULT'))
				$access_id = ACCESS_DEFAULT;
			else
				$access_id = 0;
		}
	
?>
<div class="contentWrapper">
<form action="<?php echo $vars['url']; ?>action/<?php echo $action; ?>" enctype="multipart/form-data" method="post">
<?php

	if ($action == "file/upload") {

?>
		<p>
			<label><?php echo elgg_echo("file:file"); ?><br />
			<?php

				echo elgg_view("input/file",array('internalname' => 'upload'));
			
			?>
			</label>
		</p>
<?php

	}

?>
		<p>
			<label><?php echo elgg_echo("title"); ?><br />
			<?php

				echo elgg_view("input/text", array(
									"internalname" => "title",
									"value" => $title,
													));
			
			?>
			</label>
		</p>
		<p class="longtext_editarea">
			<label><?php echo elgg_echo("description"); ?><br />
			<?php

				echo elgg_view("input/longtext",array(
									"internalname" => "description",
									"value" => $description,
													));
			?>
			</label>
		</p>
		<p>
			<label><?php echo elgg_echo("tags"); ?><br />
			<?php

				echo elgg_view("input/tags", array(
									"internalname" => "tags",
									"value" => $tags,
													));
			
			?>
		</p>
<?php

		$categories = elgg_view('categories',$vars);
		if (!empty($categories)) {
?>

		<p>
			<?php echo $categories; ?>
		</p>

<?php
		}

?>
		<p>
			<label>
				<?php echo elgg_echo('access'); ?><br />
				<?php echo elgg_view('input/access', array('internalname' => 'access_id','value' => $access_id)); ?>
			</label>
		</p>
	
		<p>
			<?php

				if (isset($vars['container_guid']))
					echo "<input type=\"hidden\" name=\"container_guid\" value=\"{$vars['container_guid']}\" />";
				if (isset($vars['entity']))
					echo "<input type=\"hidden\" name=\"file_guid\" value=\"{$vars['entity']->getGUID()}\" />";
			
			?>
			<input type="submit" value="<?php echo elgg_echo("save"); ?>" />
		</p>

</form>
</div>