<?php
    $widget = $vars['widget'];

    $esc_url = escape(abs_url($widget->get_url()));
?>
<item>
  <guid isPermaLink='true'><?php echo $esc_url; ?></guid>
  <pubDate><?php echo date("r",$widget->time_published); ?></pubDate>
  <link><?php echo $esc_url; ?></link>
  <title><?php echo escape($widget->get_title()); ?></title>
  <description><?php echo escape($widget->render_content()); ?></description>
</item>
