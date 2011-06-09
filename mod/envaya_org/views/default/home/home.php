<?php
    $region = GeoIP::get_world_region();
    $is_africa = ($region == Geography::Africa);
    $is_supported = GeoIP::is_supported_country();
    
    $defaultPhoto = "/_media/images/home/banner_planting5.jpg";
    
    PageContext::add_header_html("        
<noscript>
<style type='text/css'>
#home_banner_photo { background-image:url($defaultPhoto); }
.slideshow_shadow { display:none; }
</style>
</noscript>       
<!--[if lte IE 7]>
<style type='text/css'>
.home_banner_text h1 .centered { display:inline; }
</style>
<![endif]-->       
");
?>

<div class='home_content_bg'>
<div id='home_banner'>
<div class='home_banner_text'>
<div style='text-align:center;padding-top:10px;padding-left:10px'>
<a href='/envaya' class='home_logo' title='Envaya'></a>
</div>
<h1>
<div class='centered'>
<?php 

if ($is_africa)
{
    echo __('home:heading_html_africa');
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
</div>

<script type='text/javascript'>
<?php echo view('js/slideshow'); ?>
slideshow(<?php 
    try
    {
        echo FeaturedPhoto::get_json_array(); 
    }
    catch (DatabaseException $ex) 
    {    
        echo '[]';
    }
?>, <?php echo json_encode($defaultPhoto); ?>);
</script>

<div class='home_sticker'>

<?php
    if ($is_supported)
    {
?>

<div class='home_sticker_label'><?php echo __('home:sign_up_heading'); ?></div>
<a class='home_button' href='/org/new'><span><?php echo __('home:sign_up_button'); ?></span></a>

<?php
    }
    else    
    {
?>

<div class='home_sticker_label' style='padding-top:28px;font-size:14px'><?php echo __('home:see_news'); ?></div>
<a class='home_button' href='/pg/feed' style='margin-top:9px'><span style='font-size:15px;padding-top:9px'><?php echo __('home:view_updates'); ?></span></a>

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

    if ($is_africa)
    {
        echo view('home/about_africa'); 
    }
    else 
    {
        echo view('home/about'); 
    }
    
    echo "<div style='padding:8px 0px'>".view('home/follow')."</div>"; 
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