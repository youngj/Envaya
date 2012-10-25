<?php
    $region = GeoIP::get_world_region();
    $is_africa = ($region == Geography::Africa);
    $is_available = Geography::is_available_country(GeoIP::get_country_code());
    
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
<!--[if IE 6]>
<style type='text/css'>
.home_about, .home_content {background-image:none}
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
<?php echo view('js/slideshow'); ?>
<script type='text/javascript'>
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
    if ($is_available)
    {
?>

<div class='home_sticker_label'><?php echo __('home:sign_up_heading'); ?></div>
<a class='home_button' href='/org/new'><span><?php echo __('home:sign_up_button'); ?></span></a>

<?php
    }
    else    
    {
?>

<div class='home_sticker_label'><?php echo __('home:see_news'); ?></div>
<a class='home_button' href='/pg/feed'><span><?php echo __('home:view_updates'); ?></span></a>

<?php
    }
?>
</div>
</div>

</div>
<table class='home_table'>
<tr>
<td width='221' height='181'>
<?php echo view('home/for_organizations'); ?>
</td>
<td width='236' height='181'>
<?php echo view('home/for_everyone'); ?>
</td>
<td width='363' rowspan='2' class='home_bottom_right'>
<div class='home_section home_section_about'>
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
    
    //echo "<div style='padding:0px'>".view('home/follow')."</div>"; 
?>
    </div>   
</div>
<div class='home_section home_section_right'>
    <div class='home_heading heading_gray'><h4><?= __('home:featured_items'); ?></h4></div>
    <div class='home_about'>
    <div style='position:relative;height:400px'>
    <div id='live_feed' onmouseover='feedHover=1' onmouseout='feedHover=0' style='height:400px;overflow:hidden'>
    <?php
    
    $items_html = FeedItem::get_featured_items_html();
    
    $num_items = sizeof($items_html);
    $rand_offset = $num_items ? mt_rand(0, $num_items - 1) : 0;
    
    for ($i = 0; $i < $num_items; $i++)
    {
        echo $items_html[($i + $rand_offset) % $num_items];
    }
    ?>    
    </div>
    <div style='position:absolute;top:385px;height:15px;width:345px;background:url(/_media/images/home/fadeout.png) repeat-x left bottom'></div>
    </div>
    <?php if ($num_items > 4) { ?>
    <script type='text/javascript'>
    function rotateLiveFeed()
    {
        if (window.feedHover) return;
        var container = $('live_feed'), last = container.lastChild;
        if (last)
        {
            removeElem(last);
            container.insertBefore(last, container.firstChild || null);
        }
    }
    setInterval(rotateLiveFeed, 6000);    
    </script>
    <?php } ?>
    <div style='text-align:right;padding-top:5px'>
    <a href='/pg/feed'><?= __('home:more_updates'); ?></a>
    </div>
    </div>
</div>
</td>
</tr>
<tr>
<td colspan='2' width='420' height='457' class='home_bottom_left'>
<?php
    echo view('home/featured_site'); 
?>
</td>
</tr>
</table>
<div style='height:4px'></div>