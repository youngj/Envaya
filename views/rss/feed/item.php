<?php
    $feedItem = $vars['item'];
    $org = $feedItem->get_user_entity();
    $subject = $feedItem->get_subject_entity();
?>
<item>
  <guid isPermaLink='true'><?php echo escape($subject->get_url()); ?></guid>
  <pubDate><?php echo date("r",$feedItem->time_posted) ?></pubDate>
  <link><?php echo escape($subject->get_url()); ?></link>
  <title><?php 
    echo escape(Markup::sanitize_html($feedItem->render_heading(@$vars['mode']), 
        array('HTML.AllowedElements' => '','AutoFormat.Linkify' => false,'AutoFormat.RemoveEmpty' => true)));
  ?></title>
  <description><?php echo escape($feedItem->render_content(@$vars['mode'])); ?></description>
</item>