<?php
    $developingVersion = GeoIP::is_supported_country();
    
    $defaultPhoto = "/_graphics/home/banner_planting.jpg?v5";
    
    PageContext::add_header_html('heading_css', "        
<noscript>
<style type='text/css'>
#home_banner_photo
{
    background-image:url($defaultPhoto);
}
</style>
</noscript>       
    
<!--[if lte IE 7]>
<style type='text/css'>
.home_banner_text h1 .centered
{
    display:inline;
}
</style>
<![endif]-->       
");
?>

<div class='home_content_bg'>
<div id='home_banner'>
<div class='home_banner_text'>
<div style='text-align:center;padding-top:10px;padding-left:10px'>
<a href='/envaya'>
<img src='/_graphics/home/envaya-logo-big.gif' width='300' height='61' title='Envaya' alt='Envaya' />
</a>
</div>
<h1>
<div class='centered'>

<?php 

if ($developingVersion)
{
    echo sprintf(__('home:heading_html_developing'), GeoIP::get_country_name()); 
}
else
{
    echo __('home:heading_html');     
}


?>
</div>
</h1>
</div>

<div class='slideshow_container'>
    <div id='home_banner_photo' class='slideshow_photo'></div>
    <div id='home_caption_shadow' class='slideshow_shadow'></div>
    <div id='home_slideshow_controls' class='slideshow_controls'></div>    
    <div id='home_follow_container'>
    <?php if (!$developingVersion) { ?>
        <div class='home_follow'><?php echo __('home:follow'); ?></div>
    <?php } ?>
        <a title='Facebook' href='http://www.facebook.com/Envaya' class='home_follow_icon home_follow_fb'></a>
        <a title='Twitter' href='http://twitter.com/Envaya' class='home_follow_icon home_follow_twitter'></a>
    </div>
</div>

<script type='text/javascript'>
<?php readfile(Config::get('path').'_media/inline_js/slideshow.js'); ?>
slideshow(<?php echo FeaturedPhoto::get_json_array(); ?>, <?php echo json_encode($defaultPhoto); ?>);
</script>

<div class='home_donate_sticker'>

<?php
    if ($developingVersion)
    {
?>

<div class='home_get_website'><?php echo __('home:sign_up_heading'); ?></div>
<a class='home_donate_button' href='/org/new'><span><?php echo __('home:sign_up_button'); ?></span></a>

<?php
    }
    else    
    {
?>

<div class='home_donate_difference' style='padding-top:28px;font-size:14px'><?php echo __('home:see_news'); ?></div>
<a class='home_donate_button' href='/org/feed' style='margin-top:9px'><span style='font-size:15px;padding-top:9px'><?php echo __('home:view_updates'); ?></span></a>

<?php
    }
?>
</div>
</div>

</div>
<table class='home_table'>
<tr>
<td width='221'>
<?php echo view('home/for_organizations'); ?>
</td>
<td width='236'>
<?php echo view('home/for_everyone'); ?>
</td>
<td width='363' rowspan='2' class='home_bottom_right'>
<div class='home_section home_section_right'>
    <div class='home_heading heading_gray'><h4><?php echo __("home:whatwedo") ?></h4></div>
    <div class='home_about'>   
<?php 

    if ($developingVersion)
    {
        echo view('home/about_developing'); 
    }
    else 
    {
        echo view('home/about'); 
    }

?>
    </div>   
</div>
</td>
</tr>
<tr>
<td colspan='2' width='420' class='home_bottom_left'>
<?php echo view('home/featured_site'); ?>
</td>
</tr>
</table>
<div style='height:4px'></div>
</div>
