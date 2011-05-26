<?php
    $widget = $vars['widget'];
    
    $twitter_user = $widget->get_metadata('twitter_user') ?: array();

    $image_url = @$twitter_user['image_url'];
    $screen_name = @$twitter_user['screen_name'];
    $name = @$twitter_user['name'];
    
?>
<?php if ($image_url) { ?>
<a class="image_left" style='padding-top:3px' href="http://twitter.com/<?php echo escape($screen_name); ?>">
<img height="48" width="48" src="<?php echo escape($image_url); ?>" alt="<?php echo escape($name); ?>"></a>
<?php } ?>
<strong><a href="http://twitter.com/<?php echo escape($screen_name); ?>"><?php echo escape($screen_name); ?></a></strong>
<p class='last-paragraph'><?php echo $widget->render_content(); ?></p>
