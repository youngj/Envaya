<?php

	$title = $vars['entity']->title;
	if (empty($title)) {
		$subtitle = strip_tags($vars['entity']->description);
		$title = substr($subtitle,0,32);
		if (strlen($subtitle) > 32)
			$title .= " ...";
	}

?>

	<item>
	  <guid isPermaLink='true'><?php echo htmlspecialchars($vars['entity']->get_url()); ?></guid>
	  <pubDate><?php echo date("r",$vars['entity']->time_created) ?></pubDate>
	  <link><?php echo htmlspecialchars($vars['entity']->get_url()); ?></link>
	  <title><![CDATA[<?php echo $title; ?>]]></title>
	  <description><![CDATA[<?php echo (Markup::autop($vars['entity']->description)); ?>]]></description>
	  <?php
			$owner = $vars['entity']->get_owner_entity();
			if ($owner)
			{
?>
	  <dc:creator><?php echo $owner->name; ?></dc:creator>
<?php
			}
	  ?>
	  <?php
			if (
				($vars['entity'] instanceof Locatable) &&
				($vars['entity']->get_longitude()) &&
				($vars['entity']->get_latitude())
			) {
				?>
				<georss:point><?php echo $vars['entity']->get_latitude(); ?> <?php echo $vars['entity']->get_longitude(); ?></georss:point>
				<?php
			}
	  ?>
	  <?php echo view('extensions/item'); ?>
	</item>
