<div class='home_featured'>
<h4 class='home_featured_heading'><?php echo __('featured:home_heading') ?></h4>
<div class='home_featured_content'>
<?php

$activeSite = FeaturedSite::get_active();
if ($activeSite)
{
    echo view_entity($activeSite, array('show_date' => false));
}
?>

<a class='home_more' href='/org/featured'><?php echo __('featured:see_all') ?></a>
<div style='clear:both'></div>
</div>
</div>