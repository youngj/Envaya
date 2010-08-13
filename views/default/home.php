<div class='home_content_bg'>
<div class='home_banner'>
<div class='home_banner_text'>
<h1><?php echo __('home:heading') ?></h1>
<h2><?php echo __('home:heading2') ?></h2>
</div>
</div>
<table class='home_table'>
<tr>
<td width='221'>
<div class='home_section home_section_left'>
    <div class='home_heading heading_blue'><div><?php echo __("home:for_organizations") ?></div></div>
    <div class='home_content'>
        <a class='icon_link icon_signup' href='org/new'><?php echo __("home:sign_up") ?></a>
        <a class='icon_link icon_help' href='envaya/why'><?php echo __("home:why") ?></a>
        <a class='icon_link icon_logout' href='pg/login'><?php echo __("login") ?></a>
    </div>
</div>
</td>
<td width='236'>
<div class='home_section home_section_left'>
    <div class='home_heading heading_green'><div><?php echo __("home:for_everyone") ?></div></div>
    <div class='home_content'>
        <a class='icon_link icon_explore' href='org/browse'><?php echo __("browse:title") ?></a>
        <a class='icon_link icon_search' href='org/search'><?php echo __("search:title") ?></a>
        <a class='icon_link icon_feed' href='org/feed'><?php echo __("feed:title") ?></a>
    </div>
</div>
</td>
<td width='363' rowspan='2' class='home_bottom_right'>
<div class='home_section home_section_right'>
    <div class='home_heading heading_gray'><div><?php echo __("home:whatwedo") ?></div></div>
    <div class='home_about'>   
<p class='last-paragraph'>
<?php echo __('home:description').' '; ?>
<a class='home_more' href='/envaya'><?php echo __('home:learn_more') ?></a>
</p>
<div style='text-align:center;padding:8px;'>
<a href='/envaya' style='margin:0 auto;display:block;width:200px;height:150px;background-image:url(_graphics/dar_conference_smiling.jpg)'></a>
</div>
<p class='last-paragraph'>
<?php echo __('home:donate') ?>
</p>
<div style='padding-top:6px;text-align:center'>
<?php echo view('page/donate_button'); ?>
</div>
    </div>
</div>
</td>
</tr>
<tr>
<td colspan='2' width='420' class='home_bottom_left'>
<div class='home_featured'>
<div class='home_featured_heading'><?php echo __('featured:home_heading') ?></div>
<div class='home_featured_content'>
<?php
$activeSite = FeaturedSite::query()->where('active=1')->get();
if ($activeSite)
{
    echo view_entity($activeSite);
}
?>

<a class='home_more' href='org/featured'><?php echo __('featured:see_all') ?></a>
</div>
</div>
</td>
</tr>
</table>
<div style='height:4px'></div>
</div>
