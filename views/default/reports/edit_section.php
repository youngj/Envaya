<?php
$report = $vars['report'];
$content = $vars['content'];
$section_id = $vars['section_id'];
$sections = $report->get_handler()->get_sections();
$num_sections = sizeof($sections);
?>
<script type='text/javascript'>
function setNextSection($section)
{
    document.getElementById('next_section').value = $section;
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

echo "<div class='repor t_section_nav'>";
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
        $links[] = "<a href='javascript:void(0)' onclick='setSubmitted(); setNextSection($i); document.forms[0].submit()'>".
            escape($section_title)."</a> ";            
    }
}
echo implode(" &middot; ", $links);

echo "</div>";

echo "<h2 class='report_section_heading'>".escape(sprintf(__('report:section_heading'), $section_id, $vars['section']['title']))."</h2>";

echo $content;

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
