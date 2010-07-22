<?php
    $feedItems = $vars['items'];

if (empty($feedItems))
{
    echo "<div class='padded'>".__("search:noresults")."</div>";
}

foreach ($feedItems as $feedItem)
{
    $org = $feedItem->getUserEntity();
    $subject = $feedItem->getSubjectEntity();
    if ($org && $subject)
    {
    ?>

    <div class='blog_post_wrapper padded'>
    <div class="feed_post">
        <?php
            echo $feedItem->renderView($mode='self');
        ?>
        <div class='blog_date'><?php echo $feedItem->getDateText() ?></div>
        <div style='clear:both'></div>
    </div>
    </div>
    <?php
    }
}
