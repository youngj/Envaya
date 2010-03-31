<?php
    $org = get_loggedin_user();
?>

<p>
<?php echo elgg_echo('help:summary1') ?>
</p>
<p>
<?php echo elgg_echo('help:summary2') ?>
</p>
<p>
<?php echo elgg_echo('help:summary3') ?>
</p>

<h3><?php echo elgg_echo('help:contents') ?></h3>
<ul style='font-weight:bold'>
    <li><a href='org/help#viewing'><?php echo elgg_echo('help:viewing') ?></a></li>    
    <li><a href='org/help#editing'><?php echo elgg_echo('help:editing') ?></a></li>
    <li><a href='org/help#settings'><?php echo elgg_echo('help:settings') ?></a></li>
    <li><a href='org/help#home'><?php echo elgg_echo('widget:home') ?></a></li>
    <li><a href='org/help#news'><?php echo elgg_echo('help:news') ?></a></li>
    <li><a href='org/help#other'><?php echo elgg_echo('help:other') ?></a></li>
    <li><a href='org/help#connecting'><?php echo elgg_echo('help:connecting') ?></a></li>
</ul>

<h3 id='viewing'><?php echo elgg_echo('help:viewing') ?></h3>

<p>
<?php echo elgg_echo('help:viewing:url') ?>
 <br /><strong><a href='<?php echo $org->getURL() ?>'><?php echo $org->getURL() ?></a></strong>
</p> 

<?php if (!$org->isApproved()) { ?>

<p>
<?php echo elgg_echo('help:viewing:hidden') ?>
</p>


<?php } else { ?>

<p>
<?php echo elgg_echo('help:viewing:approved') ?>
</p>


<?php } ?>

<h3 id='editing'><?php echo elgg_echo('help:editing') ?></h3>

<p>
<?php echo elgg_echo('help:editing:onlyyou') ?>
</p>

<p>
<?php echo sprintf(elgg_echo('help:editing:credentials'), "<strong>{$org->username}</strong>") ?>
</p>

<p>
<?php echo sprintf(elgg_echo('help:editing:editsite'), 
    "<strong><a href='pg/dashboard'>".elgg_echo('dashboard')."</a></strong>",
    "<a href='pg/dashboard' target='_blank'><img class='icon_with_bg' src='_graphics/pencil.gif?v2' /></a>") ?>
</p>

<p>
<?php echo elgg_echo('help:editing:background') ?>
</p>

<p>
<?php echo sprintf(elgg_echo('help:editing:logout'), "<img class='icon_with_bg' src='_graphics/logout.gif?v2' />") ?>
</p>

<h3 id='settings'><?php echo elgg_echo('help:settings') ?></h3>

<p>
<?php echo sprintf(elgg_echo('help:settings:icon'), 
    "<a href='pg/settings' target='_blank'><img class='icon_with_bg' src='_graphics/settings.gif' /></a>") ?>
</p>

<p>
<?php echo elgg_echo('help:settings:youcan') ?>
</p>

<ul>
    <li><?php echo elgg_echo('help:settings:password') ?></li>
    <li><?php echo elgg_echo('help:settings:email') ?></li>
    <li><?php echo elgg_echo('help:settings:theme') ?></li>
    <li><?php echo elgg_echo('help:settings:logo') ?></li>
</ul>

<h3 id='home'><?php echo elgg_echo('widget:home') ?></h3>

<p>
<?php echo sprintf(elgg_echo('help:home:icon'), 
    "<a href='<?php echo $org->getURL() ?>' target='_blank'><img class='icon_with_bg' src='_graphics/home.gif?v2' /></a>") ?>
</p>

<p>
<?php echo elgg_echo('help:home:shows') ?>
</p>

<ul>
    <li><?php echo elgg_echo('help:home:mission') ?></li>
    <li><?php echo elgg_echo('help:home:news') ?></li>
    <li><?php echo elgg_echo('help:home:sectors') ?></li>
    <li><?php echo elgg_echo('help:home:location') ?></li>
</ul>

<h3 id='news'><?php echo elgg_echo('help:news') ?></h3>

<p>
<?php echo sprintf(elgg_echo('help:news:about'), 
    "<strong><a href='{$org->getURL()}/news'>".elgg_echo('widget:news')."</a></strong>") ?>
</p>

<p>
<?php echo sprintf(elgg_echo('help:news:feed'), 
    "<strong><a href='org/feed'>".elgg_echo('feed:title')."</a></strong>") ?>
</p>

<p>
<?php echo elgg_echo('help:news:mobile') ?>
</p>

<ul>
<li><strong><?php echo elgg_echo('widget:news:email') ?></strong>: 
<?php echo sprintf(elgg_echo('widget:news:email:summary'), "<strong>{$org->getPostEmail()}</strong>") ?>
</li>
<li><strong><?php echo elgg_echo('widget:news:sms') ?></strong>: 
<?php echo elgg_echo('widget:news:sms:summary') ?></li>
</ul>

<h3 id='other'><?php echo elgg_echo('help:other') ?></h3>

<p>
<?php echo sprintf(elgg_echo('help:other:summary'), "<a href='pg/dashboard'>".elgg_echo('dashboard')."</a>") ?>
</p>
<ul>
<li><strong><a href='<?php echo $org->getURL() ?>/projects/edit?from=org/help%23other'><?php echo elgg_echo('widget:projects') ?></a></strong>: 
    <?php echo elgg_echo('help:other:projects') ?>   
</li>
<li><strong><a href='<?php echo $org->getURL() ?>/history/edit?from=org/help%23other'><?php echo elgg_echo('widget:history') ?></a></strong>: 
    <?php echo elgg_echo('help:other:history') ?>
</li>
<li><strong><a href='<?php echo $org->getURL() ?>/team/edit?from=org/help%23other'><?php echo elgg_echo('widget:team') ?></a></strong>: 
    <?php echo elgg_echo('help:other:team') ?>
</li>
<li><strong><a href='<?php echo $org->getURL() ?>/contact/edit?from=org/help%23other'><?php echo elgg_echo('widget:contact') ?></a></strong>: 
    <?php echo elgg_echo('help:other:contact') ?>
</li>
</ul>

<h3 id='connecting'><?php echo elgg_echo('help:connecting') ?></h3>

<p>
<?php echo elgg_echo('help:connecting:summary') ?>
</p>

<h4><?php echo elgg_echo('widget:partnerships') ?></h4>

<p>
<?php echo sprintf(elgg_echo('help:connecting:partnerships'), 
    "<strong><a href='{$org->getURL()}/partnerships/edit?from=org/help%23other'>".elgg_echo('widget:partnerships')."</a></strong>") ?>
</p>

<p>
<?php echo sprintf(elgg_echo('help:connecting:partnerships:instructions'), 
    "<strong><a href='{$org->getURL()}/partnerships/edit?from=org/help%23other'>".elgg_echo('widget:partnerships')."</a></strong>") ?>
</p>

<p>
<?php echo elgg_echo('help:connecting:partnerships:invite') ?>
</p>

<h4><?php echo elgg_echo('help:connecting:messages') ?></h4>
<p>
<?php echo sprintf(elgg_echo('help:connecting:messages:instructions'), "<strong>".elgg_echo('message:link')."</strong>") ?>
</p>

<?php if (!$org->isApproved()) { ?>
<p>
<em>
<?php echo elgg_echo('help:connecting:disabled') ?>
</p>
<?php } ?>