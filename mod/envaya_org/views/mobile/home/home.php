<div id='topbar2'>
<a href='/envaya'><?php echo __('about') ?></a> &nbsp;
<a href='/pg/browse'><?php echo __('browse') ?></a> &nbsp;
<a href='/pg/search'><?php echo __('search') ?></a> &nbsp;
<a href='/pg/feed'><?php echo __('feed') ?></a> &nbsp; 
<?php echo view('page_elements/login_area', $vars); ?>
</div>
<div class='padded'>
<p>
 <?php echo __('home:description') ?>
 <a href='/envaya'><?php echo __('home:learn_more') ?></a>
</p>
<?php echo view('home/featured_site'); ?>
</div>