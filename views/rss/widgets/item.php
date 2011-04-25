<?php
    $widget = $vars['widget'];
?>
<item>
  <guid isPermaLink='true'><?php echo escape($widget->get_url()); ?></guid>
  <pubDate><?php echo date("r",$widget->time_published); ?></pubDate>
  <link><?php echo escape($widget->get_url()); ?></link>
  <title><?php echo escape($widget->get_title()); ?></title>
  <description><?php echo escape($widget->render_content()); ?></description>
</item>
