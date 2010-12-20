<?php
$report = $vars['report'];
$content = $vars['content'];
$section_id = $vars['section_id'];
$sections = $report->get_handler()->get_sections();
$num_sections = sizeof($sections);

    ob_start();
?>
<!--[if gte IE 5.5]>
<![if lt IE 7]>
<style type="text/css">

div#floating_save
{
    position: absolute;
    bottom:auto;    
    overflow:hidden;
    z-index:1000;
    width: expression( ( document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body.clientWidth ) + 'px' );
    top: expression( ( -43 + ( document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight ) +(ignoreMe =  document.documentElement.scrollTop ? document.documentElement.scrollTop :  document.body.scrollTop ) ) + 'px' );
}

</style>
<![endif]>
<![endif]-->
<?php
    $ie6_css = ob_get_clean();
    PageContext::add_header_html('edit_section_ie6_css', $ie6_css);

?>
<script type='text/javascript'>
function setNextSection($section)
{
    document.getElementById('next_section').value = $section;
    var now = new Date();
    document.getElementById('user_save_time').value = "" + (now.getTime()/1000 - now.getTimezoneOffset() * 60);
    return true;
}

function goToSection($i)
{
    setTimeout(function() {
        setSubmitted();     
        setNextSection($i);     
        document.forms[0].submit();
    }, 10);    
}

function saveChanges()
{
    setTimeout(function() {
        setSubmitted();
        setScrollPosition();
        setNextSection(<?php echo $section_id; ?>);
        document.forms[0].submit();
    }, 10);
}

function setScrollPosition()
{
    var scrollY = window.pageYOffset || window.document.documentElement.scrollTop || window.document.body.scrollTop;
    document.getElementById('scroll_position').value = "" + scrollY;
    return true;
}

var autoFunctions = {};

function updateValue($fieldName)
{
    setTimeout(function() {
        var val = autoFunctions[$fieldName]();
        if (!isNaN(val))
        {
            getField($fieldName).value = '' + val;
        }
    }, 1);
}

function getField($fieldName)
{
    return document.forms[0]['field_'+$fieldName];
}

function getInteger($fieldName)
{
    var val = getField($fieldName).value;
    if (!val)
    {
        return 0;
    }
    return parseInt(val,10);
}

</script>
<?php
echo "<div class='report_section_nav'>";
$links = array();

foreach ($sections as $i => $section)
{
    $section_title = $section['title'];
    if ($section_id == $i)
    {
        $links[] = "<span>".escape($section_title)."</span> ";
    }
    else
    {
        $links[] = "<a href='javascript:void(0)' onclick='goToSection($i)'>".
            escape($section_title)."</a> ";            
    }
}

$links[] = "<a href='javascript:void(0)' onclick='goToSection(\"\")'>".
    __('report:preview_submit')."</a> ";            


echo implode(" &middot; ", $links);

echo "</div>";
?>

<?php
if ($section_id == 1) {
?>

<div class='report_preview_message' style='font-size:12px'>
    <p><?php echo __('report:start_message'); ?></p>
    <ol>
    <li><?php echo __('report:start_message_0'); ?></li>
    <li><?php echo sprintf(__('report:start_message_1'), "<em>".__('report:save_changes')."</em>"); ?></li>
    <li><?php echo sprintf(__('report:start_message_2'), "<em>".__('report:next_page')."</em>"); ?></li>
    <li><?php echo sprintf(__('report:start_message_3'), "<strong style='white-space:nowrap'>{$report->get_report_definition()->get_url()}/start</strong>"); ?></li>
    <li><?php echo sprintf(__('report:start_message_4'), 
        escape($report->get_report_definition()->get_container_entity()->name),
        "<em>".__('report:preview_submit')."</em>"
    ); ?></li>
    </ol>
</div>

<?php
}
?>

<?php
echo "<h2 class='report_section_heading'>".escape(sprintf(__('report:section_heading'), 
    $section_id, 
    $num_sections,
    $vars['section']['title'])
)."</h2>";

?>
<?php
echo $content;

echo view('input/hidden', array(
    'internalname' => 'user_save_time',
    'internalid' => 'user_save_time',
));

echo view('input/hidden', array(
    'internalname' => 'scroll_position',
    'internalid' => 'scroll_position',
));

echo view('input/hidden', array('internalname' => 'section', 'value' => $section_id)); 

echo view('input/hidden', array(
    'internalname' => 'next_section',
    'internalid' => 'next_section',
    'value' => '',
));

if ($section_id < $num_sections)
{
    echo view('input/submit', array(
        'internalname' => '_submit',
        'value' => __('report:next_page'), 
        'js' => "onclick='return setSubmitted() && setNextSection(".($section_id+1).")'"
    ));
}
else
{
    echo view('input/submit', array(
        'internalname' => '_submit',
        'value' => __('report:next_page'), 
        'trackDirty' => true
    ));
}

?>
<div id='floating_save'>
<div class='floating_save_content'>
<div class='last_save_time'>

<span id='last_save_time'>
<?php 
if ($report->user_save_time) 
{ 
    if (abs($report->user_save_time - time()) > 86400) // a bit fuzzy since this compares server time to the user's computer time
    {    
        $date_str = get_date_text($report->user_save_time);
    }
    else
    {
        $date_str = '';
    }

    $time_str = date("g:i a", $report->user_save_time);     

    echo sprintf(__('report:saved_at'), $date_str, $time_str);
} 
else 
{ 
    echo sprintf(__('report:not_saved'));
 } 
?>
</span>
<span id='saved_now' style='display:none'>
<strong><?php
    echo __('report:saved');
?></strong>
</span>
</div>

<?php

if (get_input('saved'))
{
?>
<script type='text/javascript'>
setTimeout(function() {
    document.getElementById('saved_now').style.display = 'inline';
    document.getElementById('last_save_time').style.display = 'none';    
    
    setTimeout(function()
    {
        document.getElementById('saved_now').style.display = 'none';
        document.getElementById('last_save_time').style.display = 'inline';        
    }, 5000);
    
}, 1);
</script>
<?php
}

echo view('input/submit', array(
    'internalname' => '_save',
    'value' => __('report:save_changes'), 
    'js' => "onclick='return setSubmitted() && setScrollPosition() && setNextSection(".($section_id).")'"
));
?>

</div>
</div>