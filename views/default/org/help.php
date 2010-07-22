<?php
    $org = $vars['org'];
?>

<p>
<?php echo __('help:summary1') ?>
</p>
<p>
<?php echo __('help:summary2') ?>
</p>
<p>
<?php echo __('help:summary3') ?>
</p>

<h3><?php echo __('help:contents') ?></h3>
<ul style='font-weight:bold'>
    <li><a href='org/help#viewing'><?php echo __('help:viewing') ?></a></li>
    <li><a href='org/help#editing'><?php echo __('help:editing') ?></a></li>
    <li><a href='org/help#design'><?php echo __('help:design') ?></a></li>
    <li><a href='org/help#settings'><?php echo __('help:settings') ?></a></li>
    <li><a href='org/help#home'><?php echo __('widget:home') ?></a></li>
    <li><a href='org/help#news'><?php echo __('help:news') ?></a></li>
    <li><a href='org/help#other'><?php echo __('help:other') ?></a></li>
    <li><a href='org/help#connecting'><?php echo __('help:connecting') ?></a></li>
</ul>

<h3 id='viewing'><?php echo __('help:viewing') ?></h3>

<p>
<?php echo __('help:viewing:url') ?>
 <br /><strong><a href='<?php echo $org->getURL() ?>'><?php echo $org->getURL() ?></a></strong>
</p>

<?php if (!$org->isApproved()) { ?>

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
    "<strong><a href='pg/dashboard'>".__('dashboard')."</a></strong>",
    "<a href='pg/dashboard' target='_blank'><img class='icon_with_bg' src='_graphics/pencil.gif?v3' /></a>") ?>
</p>

<p>
<?php echo __('help:editing:background') ?>
</p>

<p>
<?php echo sprintf(__('help:editing:logout'), "<img class='icon_with_bg' src='_graphics/logout.gif?v2' />") ?>
</p>


<h3 id='design'><?php echo __('help:design') ?></h3>

<p>
<?php echo sprintf(__('help:design:intro'),
    "<strong><a href='pg/dashboard'>".__('dashboard')."</a></strong>",
    "<strong><a href='{$org->getURL()}/design'>".__('design:edit')."</a></strong>")
    ?>
</p>
<ul>
    <li><?php echo __('help:design:logo') ?></li>
    <li><?php echo __('help:design:theme') ?></li>
</ul>

<h3 id='settings'><?php echo __('help:settings') ?></h3>

<p>
<?php echo sprintf(__('help:settings:icon'),
    "<a href='{$org->getURL()}/settings' target='_blank'><img class='icon_with_bg' src='_graphics/settings.gif' /></a>") ?>
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
    "<a href='{$org->getURL()}' target='_blank'><img class='icon_with_bg' src='_graphics/home.gif?v2' /></a>") ?>
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
    "<strong><a href='{$org->getURL()}/news'>".__('widget:news')."</a></strong>") ?>
</p>

<p>
<?php echo sprintf(__('help:news:feed'),
    "<strong><a href='org/feed'>".__('feed:title')."</a></strong>") ?>
</p>

<p>
<?php echo __('help:news:mobile') ?>
</p>

<ul>
<li><strong><?php echo __('widget:news:email') ?></strong>:
<?php echo sprintf(__('widget:news:email:summary'), "<strong>{$org->getPostEmail()}</strong>") ?>
</li>
<li><strong><?php echo __('widget:news:sms') ?></strong>:
<?php echo __('widget:news:sms:summary') ?></li>
</ul>

<h3 id='other'><?php echo __('help:other') ?></h3>

<p>
<?php echo sprintf(__('help:other:summary'), "<a href='pg/dashboard'>".__('dashboard')."</a>") ?>
</p>
<ul>
<li><strong><a href='<?php echo $org->getURL() ?>/projects/edit?from=org/help%23other'><?php echo __('widget:projects') ?></a></strong>:
    <?php echo __('help:other:projects') ?>
</li>
<li><strong><a href='<?php echo $org->getURL() ?>/history/edit?from=org/help%23other'><?php echo __('widget:history') ?></a></strong>:
    <?php echo __('help:other:history') ?>
</li>
<li><strong><a href='<?php echo $org->getURL() ?>/team/edit?from=org/help%23other'><?php echo __('widget:team') ?></a></strong>:
    <?php echo __('help:other:team') ?>
</li>
<li><strong><a href='<?php echo $org->getURL() ?>/contact/edit?from=org/help%23other'><?php echo __('widget:contact') ?></a></strong>:
    <?php echo __('help:other:contact') ?>
</li>
</ul>

<h3 id='connecting'><?php echo __('help:connecting') ?></h3>

<p>
<?php echo __('help:connecting:summary') ?>
</p>

<h4><?php echo __('widget:partnerships') ?></h4>

<p>
<?php echo sprintf(__('help:connecting:partnerships'),
    "<strong><a href='{$org->getURL()}/partnerships/edit?from=org/help%23other'>".__('widget:partnerships')."</a></strong>") ?>
</p>

<p>
<?php echo sprintf(__('help:connecting:partnerships:instructions'),
    "<strong><a href='{$org->getURL()}/partnerships/edit?from=org/help%23other'>".__('widget:partnerships')."</a></strong>") ?>
</p>

<p>
<?php echo __('help:connecting:partnerships:invite') ?>
</p>

<h4><?php echo __('help:connecting:messages') ?></h4>
<p>
<?php echo sprintf(__('help:connecting:messages:instructions'), "<strong>".__('message:link')."</strong>") ?>
</p>

<?php if (!$org->isApproved()) { ?>
<p>
<em>
<?php echo __('help:connecting:disabled') ?>
</p>
<?php } ?>