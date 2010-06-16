<?php
	$feedItems = $vars['items'];

if (empty($feedItems))
{
    echo "<div class='padded'>".elgg_echo("search:noresults")."</div>";
}

$isAdmin = isadminloggedin();

foreach ($feedItems as $feedItem)
{
	$org = $feedItem->getUserEntity();
	$subject = $feedItem->getSubjectEntity();
	if ($org && $subject)
	{
		$orgIcon = $org->getIcon('small');
		$orgUrl = $org->getURL();

	?>

	<div class='blog_post_wrapper padded'>
	<div class="feed_post">
    	<a class='feed_org_icon' href='<?php echo $orgUrl ?>'><img src='<?php echo $orgIcon ?>' /></a>
		<div class='feed_content'>
		<?php
			/*
			if ($isAdmin)
			{

				echo "<div class='adminFeed'>";
				if ($feedItem->featured)
				{
					echo elgg_view('output/confirmlink', array(
						'text' => elgg_echo('feed:unfeature'),
						'is_action' => true,
						'href' => "action/org/setFeatured?feed_item={$feedItem->id}&featured=0"
					));
				}
				else
				{
					echo elgg_view('output/confirmlink', array(
						'text' => elgg_echo('feed:feature'),
						'is_action' => true,
						'href' => "action/org/setFeatured?feed_item={$feedItem->id}&featured=1"
					));
				}

				echo "</div>";
			}
			*/


			echo $feedItem->renderView();
		?>
		<div class='blog_date'><?php echo $feedItem->getDateText() ?></div>
		</div>
		<div style='clear:both'></div>
	</div>
	</div>
	<?php
	}
}
