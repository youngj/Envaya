<?php if ($vars['profile_image_url']) { ?>
<a class="image_left" style='padding-top:3px' href="http://twitter.com/<?php echo escape($vars['screen_name']); ?>">
<img height="48" width="48" src="<?php echo escape($vars['profile_image_url']); ?>" alt="<?php echo escape($vars['name']); ?>"></a>
<?php } ?>
<strong><a href="http://twitter.com/<?php echo escape($vars['screen_name']); ?>"><?php echo escape($vars['screen_name']); ?></a></strong>
<p class='last-paragraph'><?php echo $vars['content']; ?></p>
