	<item>
	  <guid isPermaLink='true'><?php echo $vars['entity']->get_url(); ?></guid>
	  <pubDate><?php echo date("r",$vars['entity']->time_created) ?></pubDate>
	  <link><?php echo $vars['entity']->get_url(); ?></link>
	  <title><![CDATA[<?php echo (($vars['entity']->name)); ?>]]></title>
	  <description><![CDATA[<?php echo (Markup::autop($vars['entity']->description)); ?>]]></description>
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
