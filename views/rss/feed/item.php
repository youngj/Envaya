<?php
    $feedItem = $vars['item'];
    $org = $feedItem->get_user_entity();
    $subject = $feedItem->get_subject_entity();

    $esc_url = escape(abs_url($subject->get_url()));
?>
<item>
  <guid isPermaLink='true'><?php echo $esc_url; ?></guid>
  <pubDate><?php echo date("r",$feedItem->time_posted) ?></pubDate>
  <link><?php echo $esc_url; ?></link>
  <title><?php 
    echo escape(Markup::sanitize_html($feedItem->render_heading(@$vars['mode']), 
        array('HTML.AllowedElements' => '','AutoFormat.Linkify' => false,'AutoFormat.RemoveEmpty' => true)));
  ?></title>
  <description><?php echo escape($feedItem->render_content(@$vars['mode'])); ?></description>
</item>
