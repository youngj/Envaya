<div class='section_content'>
<?php

    $post = $vars['post'];              
    $url = rewrite_to_current_domain($post->get_url());
    $org = $post->get_root_container_entity();
    $blogDates = $org->get_blog_dates();
?>

<div style='clear:both'></div>
<div class='padded'>

<?php

if (sizeof($blogDates) > 1)
{

?>

<div id='blogTimeline'>
<a id='blogNavPrev' href='<?php echo "$url/prev"; ?>'></a>
<a id='blogNavNext' href='<?php echo "$url/next"; ?>'></a>
<div id='blogTimelineLeft'></div>
<div id='blogTimelineLine'></div>
<div id='blogTimelineRight'></div>
<div id='hoverPost' class='dropdown'>
    <div id='hoverTitle' class='dropdown_title'></div>
    <div id='hoverContent' class='dropdown_content'></div>
</div>
</div>

<script type='text/javascript'>
var blogDates = <?php echo json_encode($blogDates) ?>;

var timeline = document.getElementById('blogTimeline');

var firstTime = parseInt(blogDates[0].time_created);
var lastTime = parseInt(blogDates[blogDates.length-1].time_created);
var now = new Date();

var timeSpan = lastTime - firstTime;
var orgUrl = <?php echo json_encode($org->get_url()) ?>;

var width = 480;
function getPosForTime(time, elemWidth)
{
    var posFraction = (time - firstTime) / timeSpan;
    return (width * posFraction - elemWidth/2 + 31) + "px";
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

            var text = (date.getMonth() + 1) + "/" + date.getDate();

            if (date.getFullYear() != new Date().getFullYear())
            {
                text += "/" + date.getFullYear() % 100;
            }

            label.appendChild(document.createTextNode(text));
            timeline.appendChild(label);
    }
}

makeLabelForDate(new Date((firstTime + 86400) * 1000));
makeLabelForDate(new Date(lastTime * 1000));

var cur = document.createElement('div');
    cur.className = 'timelineCur';
    cur.style.left = getPosForTime(<?php echo json_encode($post->time_created) ?>, 13);
    timeline.appendChild(cur);

var hoverPost = document.getElementById('hoverPost');
var hoverTitle = document.getElementById('hoverTitle');
var hoverContent = document.getElementById('hoverContent');

var previewXHR = null;
var previewId = null;

function showPreview(post)
{
    removeChildren(hoverTitle);
    hoverTitle.appendChild(document.createTextNode(post.dateText));

    if (post.imageURL)
    {
        var img = document.createElement('img');
        img.src = post.imageURL;
        hoverContent.appendChild(img);
    }

    var div = document.createElement('div');
    div.innerHTML = post.snippetHTML;
    hoverContent.appendChild(div);
}

/* keeps the timeline from jumping on mouseover in ie */
/*@cc_on
setTimeout(function(){
    hoverPost.style.display = 'block';
    hoverPost.style.display = 'none';
}, 1);
@*/

function addTimelineLink(blogDate)
{
    var link = document.createElement('a');
    link.href = orgUrl + "/post/" + blogDate.guid;
    link.className = 'timelineLink';
    link.style.left = getPosForTime(blogDate.time_created, 4);

    timeline.appendChild(link);

    addEvent(link, 'mouseover', function() {

        if (previewId != blogDate.guid)
        {
            if (previewXHR)
            {
                previewXHR.abort();
                previewXHR = null;
            }
            previewXHR = fetchJson(orgUrl + "/post/" + blogDate.guid + "/preview", showPreview);
        }

        hoverPost.style.left = link.offsetLeft + "px";
        removeChildren(hoverTitle);

        hoverTitle.appendChild(document.createTextNode(<?php echo json_encode(__('loading')) ?>));
        removeChildren(hoverContent);
        hoverPost.style.display = 'block';
    });
    addEvent(link, 'mouseout', function() {
        hoverPost.style.display = 'none';
    });
}

for (var i = 0; i < blogDates.length; i++)
{
    addTimelineLink(blogDates[i]);
}

</script>


<?php
}            
echo view_entity($post, array('single_post' => true));

?>

</div>
</div>