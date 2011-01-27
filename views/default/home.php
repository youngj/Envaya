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
    <div class='slideshow_shadow'></div>
    <div id='home_slideshow_controls' class='slideshow_controls'></div>    
</div>

<script type='text/javascript'>
(function () {
    function shuffle(a)
    {
        var i = a.length;
        while (i > 0) {
            var j = Math.floor(Math.random() * i);
            i--;
            var tmp = a[i];
            a[i] = a[j];
            a[j] = tmp;
        }
        return a;
    }
    
    var images = shuffle(<?php echo FeaturedPhoto::get_json_array(); ?>),
        currentIndex = -1,
        caption = createElem('a'),
        orgLink = createElem('a'),
        imgContainer = document.getElementById('home_banner_photo'),
        controls = document.getElementById('home_slideshow_controls');

    if (!images.length)
    {
        imgContainer.style.backgroundImage = "url(<?php echo $defaultPhoto; ?>)";
        return;
    }    
    
    controls.appendChild(createElem('div', {className: 'slideshow_caption'}, caption, orgLink));
        
    if (images.length > 1)
    {
        controls.appendChild(createElem('div', {className: 'slideshow_nav'},
            createElem('a', {
                    className: 'slideshow_nav_prev', 
                    href:'javascript:void(0)',
                    mouseover:function() { preloadIndex(currentIndex - 1); },
                    click:function() { setCurrentIndex(currentIndex - 1); preloadIndex(currentIndex - 1); }
                }, 
                createElem('span',"\xab")
            ),
            createElem('a', {
                    className: 'slideshow_nav_next', 
                    href:'javascript:void(0)', 
                    mouseover:function() { preloadIndex(currentIndex + 1); },
                    click:function() { setCurrentIndex(currentIndex + 1); preloadIndex(currentIndex + 1); }
                }, 
                createElem('span',"\xbb")
            )
        ));
    }
    
    function preloadIndex(index)
    {
        var image = images[(index + images.length) % images.length];        
        if (!image.elem && !image.preload)
        {
            image.preload = new Image();
            image.preload.src = image.url;            
        }   
    }
        
    function setCurrentIndex(index)
    {    
        index = (index + images.length) % images.length;
    
        if (currentIndex != -1)
        {
            images[currentIndex].elem.style.display = 'none';
        }
    
        var image = images[index];
    
        if (!image.elem)
        {
            var img = image.elem = createElem('img',{src:image.url});
            img.style.left = (-image.x || 0) + "px";
            img.style.top = (-image.y || 0) + "px";
            imgContainer.appendChild(img);
        }   
        image.elem.style.display = 'block';
        
        caption.href = image.href;
        orgLink.href = image.href;
        removeChildren(orgLink);
        removeChildren(caption);
        caption.appendChild(document.createTextNode(image.caption));
        orgLink.appendChild(document.createTextNode(image.org));
        
        currentIndex = index;
    }
    
    function getStartIndex()
    {
        for (var i = 0; i < images.length; i++)
            if (images[i].weight >= Math.random())
                return i;
        return 0;
    }
    
    setCurrentIndex(getStartIndex());
})();
</script>

<?php

if (!$developingVersion)
{

?>

<div class='home_follow'><?php echo __('home:follow'); ?></div>
<a title='Facebook' href='http://www.facebook.com/pages/Envaya/109170625791670' class='home_follow_icon home_follow_fb'></a>
<a title='Twitter' href='http://twitter.com/Envaya' class='home_follow_icon home_follow_twitter'></a>

<?php 
    }
?>

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

<div class='home_donate_difference'><?php echo __('home:make_difference'); ?></div>
<a class='home_donate_button' href='/envaya/contribute'><span><?php echo __('donate'); ?></span></a>

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
