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
<?php echo view('home/for_organizations'); ?>
</td>
<td width='236'>
<?php echo view('home/for_everyone'); ?>
</td>
<td width='363' rowspan='2' class='home_bottom_right'>
<?php echo view('home/what_we_do'); ?>
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
