<?php
    $developingVersion = GeoIP::is_supported_country();
?>

<div class='home_content_bg'>
<div class='home_banner'>
<div class='home_banner_text'>
<div style='text-align:center;padding-top:10px;padding-left:10px'>
<a href='/envaya'>
<img src='/_graphics/home/envaya-logo-big.gif' width='300' height='61' title='Envaya' alt='Envaya' />
</a>
</div>
<h1><?php echo __('home:heading_html'); ?></h1>
</div>
<div class='home_banner_photo' style='background-image:url(/_graphics/home/banner_planting.jpg?v5)'></div>

<?php

if (!$developingVersion)
{

?>

<div class='home_follow_shadow'></div>
<div class='home_follow'><?php echo __('home:follow'); ?></div>
<a title='Facebook' href='http://www.facebook.com/pages/Envaya/109170625791670' class='home_follow_icon home_follow_fb'></a>
<a title='Twitter' href='http://twitter.com/EnvayaTZ' class='home_follow_icon home_follow_twitter'></a>

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
