<?php
    $feedItem = $vars['item'];
    $org = $feedItem->get_user_entity();
    $subject = $feedItem->get_subject_entity();
    if ($org && $subject)
    {
?>
	<item>
	  <guid isPermaLink='true'><?php echo escape($subject->get_url()); ?></guid>
	  <pubDate><?php echo date("r",$feedItem->time_posted) ?></pubDate>
	  <link><?php echo escape($subject->get_url()); ?></link>
	  <title><![CDATA[<?php echo 
        Markup::sanitize_html($feedItem->render_heading(), array('HTML.AllowedElements' => '','AutoFormat.RemoveEmpty' => true));
      ?>]]></title>
	  <description><![CDATA[<?php echo $feedItem->render_content(@$vars['mode']); ?>]]></description>
	</item>
<?php
    }