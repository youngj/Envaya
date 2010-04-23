<p>
<strong><?php echo elgg_echo('donate:info') ?></strong>
</p>
<p>
<?php echo elgg_echo('donate:goals') ?>
</p>
<ul>
<li><?php echo elgg_echo('donate:goal1') ?></li>
<li><?php echo elgg_echo('donate:goal2') ?></li>
<li><?php echo elgg_echo('donate:goal3') ?></li>
<li><?php echo elgg_echo('donate:goal4') ?></li>
</ul>

<p>
<?php echo elgg_echo('donate:call') ?>
</p>


<form action='http://www.trustforconservationinnovation.org/donate.php' method='POST'>

<input type="hidden" value="Envaya" name="project"/>

<?php echo elgg_view('input/submit', array('internalname' => 'submit', value => elgg_echo('donate:now'))) ?>

</form>