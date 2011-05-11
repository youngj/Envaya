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
	  <description><![CDATA[<?php echo (nl2br($vars['entity']->description)); ?>]]></description>
	  <?php
			$owner = $vars['entity']->get_owner_entity();
			if ($owner)
			{
?>
	  <dc:creator><?php echo $owner->name; ?></dc:creator>
<?php
			}
	  ?>
	</item>
