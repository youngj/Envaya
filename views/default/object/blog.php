
<?php
  
    $entity = $vars['entity'];
    $url = $entity->getURL();
    $canedit = $entity->canEdit();
    $full = $vars['full'];
    
    $nextUrl = $url . "/next";
    $prevUrl = $url . "/prev";    
    
    if ($full)
    {
        echo "<div class='blogNav'><a href='$prevUrl'>&lt; Previous</a> |  <a href='$nextUrl'>Next &gt;</a></div>";

        $org = $entity->getRootContainerEntity();
            
        ?>    
        
<div id='blogTimeline'>
<div id='blogTimelineLine'></div>
</div>

<script type='text/javascript'>
            
var blogDates = <?php echo json_encode($org->getBlogDates()) ?>;

var timeline = document.getElementById('blogTimeline');

var firstTime = parseInt(blogDates[0].time_created);
var lastTime = parseInt(blogDates[blogDates.length-1].time_created);
var now = new Date();

var timeSpan = lastTime - firstTime;
var orgUrl = <?php echo json_encode($org->getURL()) ?>;

var width = 400;

function getPosForTime(time, elemWidth)
{
    var posFraction = (time - firstTime) / timeSpan;
    return (width * posFraction - elemWidth/2) + "px";
}

var labels = {};

function makeLabelForDate(date)
{
    date = new Date(date.getFullYear(), date.getMonth(), date.getDate());

    var dateTime = date.getTime() / 1000;
    
    if (labels[dateTime])
    {
        return;
    }
    
    labels[dateTime] = 1;

    if (dateTime <= lastTime && dateTime >= firstTime)
    {
        var marker = document.createElement('div');
            marker.className = 'timelineMarker';
            marker.style.left = getPosForTime(dateTime, 2);
            timeline.appendChild(marker);

        var label = document.createElement('div');
            label.className = 'timelineLabel';
            label.style.left = getPosForTime(dateTime, 70);
            
            var text = date.getMonth() + "/" + date.getDate();
            
            if (date.getFullYear() != new Date().getFullYear())
            {
                text = date.getFullYear() % 100 + "/" . text;
            }    
            
            label.appendChild(document.createTextNode(text));
            timeline.appendChild(label);
    }    
}    

makeLabelForDate(new Date((firstTime + 86400) * 1000));
makeLabelForDate(new Date(lastTime * 1000));

var cur = document.createElement('div');    
    cur.className = 'timelineCur';
    cur.style.left = getPosForTime(<?php echo json_encode($entity->time_created) ?>, 2);
    timeline.appendChild(cur);

for (var i = 0; i < blogDates.length; i++)
{
    var blogDate = blogDates[i];
    
    var link = document.createElement('a');
    link.href = orgUrl + "/post/" + blogDate.guid;
    link.className = 'timelineLink';
    link.style.left = getPosForTime(blogDate.time_created, 8);
    link.appendChild(document.createTextNode("X"));
    
    timeline.appendChild(link);
}


</script>


    <?php
        }
        
    
    ?>
  
<div class="blog_post">    
    <?php 

        if ($entity->hasImage())
        {
            $imageSize = ($full) ? 'large' : 'small';        
            $imgLink = ($full) ? $nextUrl : $url;
            echo "<a class='{$imageSize}BlogImageLink' href='$imgLink'><img src='$url/image/$imageSize?{$entity->time_updated}' /></a>";            
        }
        
        echo view_translated($entity, 'content'); 

        if ($canedit) {

        ?>
            <div class="blogEditControls">
            <a href="<?php echo $url; ?>/edit"><?php echo elgg_echo("edit"); ?></a>  &nbsp; 
            <?php

                echo elgg_view("output/confirmlink", array(
                    'href' => $vars['url'] . "action/news/delete?blogpost=" . $entity->getGUID(),
                    'text' => elgg_echo('delete'),
                    'is_action' => true,
                    'confirm' => elgg_echo('deleteconfirm'),
                ));
            ?>
            </div>
        <?php
        }

    ?>      
      
    <p class="strapline">
        <?php echo date("M j, Y",$vars['entity']->time_created); ?>
    </p>
</div>
