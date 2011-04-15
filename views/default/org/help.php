<div class='section_content padded'>
<?php
    $org = $vars['org'];
    $curUrl = $org->get_url()."/help";
?>

<h3><?php echo __('help:contents') ?></h3>
<ul style='font-weight:bold'>
    <li><a href='<?php echo $curUrl ?>#viewing'><?php echo __('help:viewing') ?></a></li>
    <li><a href='<?php echo $curUrl ?>#editing'><?php echo __('help:editing') ?></a></li>
    <li><a href='<?php echo $curUrl ?>#design'><?php echo __('help:design') ?></a></li>
    <li><a href='<?php echo $curUrl ?>#settings'><?php echo __('help:settings') ?></a></li>
    <li><a href='<?php echo $curUrl ?>#home'><?php echo __('widget:home') ?></a></li>
    <li><a href='<?php echo $curUrl ?>#news'><?php echo __('help:news') ?></a></li>
    <li><a href='<?php echo $curUrl ?>#other'><?php echo __('help:other') ?></a></li>
    <li><a href='<?php echo $curUrl ?>#connecting'><?php echo __('help:connecting') ?></a></li>
</ul>

<h3 id='viewing'><?php echo __('help:viewing') ?></h3>

<p>
<?php echo __('help:viewing:url') ?>
 <br /><strong><a href='<?php echo $org->get_url() ?>'><?php echo $org->get_url() ?></a></strong>
</p>

<?php if (!$org->is_approved()) { ?>

<p>
<?php echo __('help:viewing:hidden') ?>
</p>


<?php } else { ?>

<p>
<?php echo __('help:viewing:approved') ?>
</p>


<?php } ?>

<h3 id='editing'><?php echo __('help:editing') ?></h3>

<p>
<?php echo __('help:editing:onlyyou') ?>
</p>

<p>
<?php echo sprintf(__('help:editing:credentials'), "<strong>{$org->username}</strong>") ?>
</p>

<p>
<?php echo sprintf(__('help:editing:editsite'),
    "<strong><a href='/pg/dashboard'>".__('dashboard:title')."</a></strong>",
    "<a href='/pg/dashboard' target='_blank'><img class='icon_with_bg' src='/_graphics/pencil.gif?v3' /></a>") ?>
</p>

<p>
<?php echo __('help:editing:background') ?>
</p>

<p>
<?php echo sprintf(__('help:editing:logout'), "<img class='icon_with_bg' src='/_graphics/logout.gif?v2' />") ?>
</p>


<h3 id='design'><?php echo __('help:design') ?></h3>

<p>
<?php echo sprintf(__('help:design:intro'),
    "<strong><a href='pg/dashboard'>".__('dashboard:title')."</a></strong>",
    "<strong><a href='{$org->get_url()}/design'>".__('design:edit')."</a></strong>")
    ?>
</p>
<ul>
    <li><?php echo __('help:design:logo') ?></li>
    <li><?php echo __('help:design:theme') ?></li>
</ul>

<h3 id='settings'><?php echo __('help:settings') ?></h3>

<p>
<?php echo sprintf(__('help:settings:icon'),
    "<a href='{$org->get_url()}/settings' target='_blank'><img class='icon_with_bg' src='/_graphics/settings.gif' /></a>") ?>
</p>

<p>
<?php echo __('help:settings:youcan') ?>
</p>

<ul>
    <li><?php echo __('help:settings:name') ?></li>
    <li><?php echo __('help:settings:password') ?></li>
    <li><?php echo __('help:settings:email') ?></li>
</ul>

<h3 id='home'><?php echo __('widget:home') ?></h3>

<p>
<?php echo sprintf(__('help:home:icon'),
    "<a href='{$org->get_url()}' target='_blank'><img class='icon_with_bg' src='/_graphics/home.gif?v2' /></a>") ?>
</p>

<p>
<?php echo __('help:home:shows') ?>
</p>

<ul>
    <li><?php echo __('help:home:mission') ?></li>
    <li><?php echo __('help:home:news') ?></li>
    <li><?php echo __('help:home:sectors') ?></li>
    <li><?php echo __('help:home:location') ?></li>
</ul>

<h3 id='news'><?php echo __('help:news') ?></h3>

<p>
<?php echo sprintf(__('help:news:about'),
    "<strong><a href='{$org->get_url()}/news'>".__('widget:news')."</a></strong>") ?>
</p>

<p>
<?php echo sprintf(__('help:news:feed'),
    "<strong><a href='org/feed'>".__('feed:title')."</a></strong>") ?>
</p>

<h3 id='other'><?php echo __('help:other') ?></h3>

<p>
<?php echo sprintf(__('help:other:summary'), "<a href='pg/dashboard'>".__('dashboard:title')."</a>") ?>
</p>
<ul>
<li><strong><a href='<?php echo $org->get_url() ?>/projects/edit'><?php echo __('widget:projects') ?></a></strong>:
    <?php echo __('help:other:projects') ?>
</li>
<li><strong><a href='<?php echo $org->get_url() ?>/history/edit'><?php echo __('widget:history') ?></a></strong>:
    <?php echo __('help:other:history') ?>
</li>
<li><strong><a href='<?php echo $org->get_url() ?>/team/edit'><?php echo __('widget:team') ?></a></strong>:
    <?php echo __('help:other:team') ?>
</li>
<li><strong><a href='<?php echo $org->get_url() ?>/contact/edit'><?php echo __('widget:contact') ?></a></strong>:
    <?php echo __('help:other:contact') ?>
</li>
</ul>

<h3 id='connecting'><?php echo __('help:connecting') ?></h3>

<p>
<?php echo __('help:connecting:summary') ?>
</p>

<h4><?php echo __('help:connecting:messages') ?></h4>
<p>
<?php echo sprintf(__('help:connecting:messages:instructions'), "<strong>".__('message:link')."</strong>") ?>
</p>

<?php if (!$org->is_approved()) { ?>
<p>
<em>
<?php echo __('help:connecting:disabled') ?>
</p>
<?php } ?>
</div>