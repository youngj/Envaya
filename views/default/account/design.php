<?php
    $user = $vars['user'];

    $curTheme = restore_input('theme_id', $user->get_design_setting('theme_id'));    
    $curCustomHeader = restore_input('custom_header', $user->get_design_setting('custom_header')) ? 1 : 0;
    
    $preview_url = $user->get_url();
    
    $preview_url = url_with_param($preview_url, '__topbar', '0');
    
    PageContext::add_header_html("
    <style type='text/css'>
    .thin_column { width: 1100px; }
    </style>
    ");    
    echo view('js/json');    
    echo view('js/create_modal_box');    
?>

<script type='text/javascript'>

var designSettings = <?php echo json_encode($user->get_design_settings()); ?>

function updatePreview()
{
    var url = urlWithParam(<?php echo json_encode($preview_url); ?>, '__preview', encodeURIComponent(JSON.serialize(designSettings)));    
    
    var previewFrame = $('preview_frame');
    
    if (previewFrame)
    {
        previewFrame.src = url;
    }
}

function setThemeOptionValue(optionName, value)
{
    $('theme_option_' + optionName).value = value;    
    
    var container = $('theme_option_patch_' + optionName);
    
    removeChildren(container);
    
    container.appendChild(makeThemeOptionPatch(optionName, value));
    
    designSettings[optionName] = value;
    
    if (window.updatePreview)
    {        
        updatePreview();
    }    
}

function getAvailablePatterns(optionName)
{
    var optionInfo = ThemeOptions[optionName];
    
    var patterns = {};    
    
    if (optionInfo)
    {
        var prefix = optionInfo.type + ':';
                
        for (var patternName in Patterns)
        {
            if (patternName.indexOf(prefix) == 0)
            {
                patterns[patternName] = Patterns[patternName];
            }
        }
    }
    else
    {
    }
    
    return patterns;
}

function makeThemeOptionPatch(optionName, value, onClick, large)
{
    var options = {
        className:'theme_patch float_left',
        href:'javascript:void(0)'
    };
    if (onClick)
    {
        options.click = onClick;
    }
        
    if (value == '')
    {
        return createElem('a', options, createElem('div', {style: {paddingTop:'8px', textAlign:'center', fontSize:'7px'}}, "None"));
    }
    else if (Patterns[value])
    {
        var style = { background:Patterns[value] } ;
        
        if (large)
        {
            style.width = '90px';
            style.height = '90px';
        }
    
        return createElem('a', options, { style: style});
    }
    else
    {
        return createElem('a', options, { style: { background: value}});
    }
}

var Patterns = <?php echo json_encode(Theme_UserSite::get_patterns()); ?>;

var ThemeOptions = <?php

    $options = array();

    foreach (Theme_UserSite::get_available_themes() as $theme)
    {
        $options[$theme::get_subtype_id()] = $theme::get_vars();
    }
    
    echo json_encode($options);
?>

function selectThemeOption(optionName, title, selectedValue)
{
    var content = createElem('div', {className:'modalBody'});

    var swatches = [
        [],
        /* ['#000','#333','#666','#999','#ccc','#ddd','#eee','#fff', '#300','#600','#900','#c00','#f00','#f03','#f06','#f09'],
        ['#003','#006','#009','#00c','#03f','#06f','#09f','#0bf', '#660','#990','#cc0','#ff0','#606','#909','#c0c','#f0f'],
        ['#030','#060','#090','#0c0','#0c3','#3c3','#6d4','#9f6', '#630','#930','#960','#c60','#c90','#f60','#f90','#fc0'], */
        ['']        
    ];
    
    for (var pattern in getAvailablePatterns(optionName))
    {
        swatches[0].push(pattern);
    }
    
    each(swatches, function(row) {
        each(row, function(value) {
            var patch = makeThemeOptionPatch(optionName, value, function() {
                setThemeOptionValue(optionName, value);
                removeElem(box);
            }, true);
            if (value == selectedValue)
            {
                patch.style.border = '2px solid #069';
                patch.style.margin = '0px';
            }
        
            content.appendChild(patch);
        });
        content.appendChild(createElem('div', {style:{clear:'left'}}));
    });
       
    var box = createModalBox({
        title: title,
        content: content,
        width:750,
        hideOk: true,
        shadowCancel: true
    });
    document.body.appendChild(box);
}

function showImageHeader(show)
{
    $('text_header').style.display = show ? 'none' : '';
    $('image_header').style.display = show ? '' : 'none';
    
    $('custom_header').value = show ? '1' : '0';
    
    designSettings.custom_header = show;
    updatePreview();    
}

function selectTheme(themeId)
{
    $('theme_id').value = themeId;
    
    var themesContainer = $('themes');
    
    var images = themesContainer.getElementsByTagName('img');
    for (var i = 0; i < images.length; i++)
    {
        images[i].style.border = '4px solid #ddd';
    }
    
    var img = $('theme_' + themeId);
    if (img)
    {
        // img.offsetLeft is incorrect in IE<=7 so manually calculate it
        
        var imageIndex = getSiblingIndex(img.parentNode);
        
        var offsetLeft = imageIndex * 125;
    
        var scrollLeft = Math.max(0, offsetLeft - 120);
    
        themesContainer.scrollLeft = scrollLeft;
    
        img.style.border = '4px solid #069';
    }
    
    designSettings.theme_id = themeId;
    
    if (window.updatePreview)
    {        
        updatePreview();
    }
}

function getSiblingIndex(node)
{
    var parent = node.parentNode;
    var siblings = parent.childNodes;
    for (var i = 0; i < siblings.length; i++)
    {
        if (siblings[i] == node)
        {
            return i;
        }
    }
    return -1;
}

function taglineChanged()
{
    designSettings.tagline = $('tagline').value;
    updatePreview();
}

function showThemeOptions()
{
    var themeId = $('theme_id').value;
    
    var options = ThemeOptions[themeId];

    var tbody;
    
    var content = createElem('div', {className:'modalBody'},
        createElem('table', 
            tbody = createElem('tbody')
        )
    );   
    
    var rows = { };
    var rowName, colName;
    
    var optionNameRe = /^(\w+)_([a-z]+)$/;
    
    for (var optionName in options)
    {
        var props = options[optionName];
    
        if (props.hidden)
        {
            continue;
        }
    
        var match = optionNameRe.exec(optionName);
        
        if (match)
        {
            rowName = match[1];
            colName = match[2];
        }
        else
        {
            rowName = optionName;
            colName = '';            
        }
        
        var row = rows[rowName];
        
        if (!row)
        {
            rows[rowName] = row = {};
        }
        
        row[colName] = optionName;
    }
    
    function addOption(optionName)
    {
        if (optionName)
        {
            var value = options[optionName]['default'];
            rowElem.appendChild(createElem('th', {style:{textAlign:'right',paddingLeft:'10px',paddingTop:'5px',paddingRight:'10px'}},optionName));
            rowElem.appendChild(createElem('td', makeThemeOptionPatch(optionName, value, null)));
        }
        else
        {
            rowElem.appendChild(createElem('td', {innerHTML:'&nbsp;'}));
            rowElem.appendChild(createElem('td', {innerHTML:'&nbsp;'}));
        }
    }
    
    for (var rowName in rows)
    {
        var props = options[optionName];
        var value = props['default'];
    
        var rowElem = createElem('tr');
        var row = rows[rowName];
        
        addOption(row.bg);
        addOption(row.color);
        
        for (var colName in row)
        {
            if (colName != 'bg' && colName != 'color')
            {
                addOption(row[colName]);
            }
        }
        
        tbody.appendChild(rowElem);
    }    
    
    var box = createModalBox({
        title: "Theme Options",
        top:50,
        width:600,
        content: content,
        shadowCancel: true,
        hideOk: true,
        cancelText: 'Close'
    });
    
    document.body.appendChild(box);
}

</script>

<form action='<?php echo $user->get_url() ?>/design' method='POST' style='float:left;width:470px;border-right:1px solid #ccc'>
<div class='section_content padded'>
<?php 
    echo view('input/securitytoken'); 
    
    echo view('input/hidden', array(
        'name' => 'custom_header',
        'id' => 'custom_header',
        'value' => $curCustomHeader,
    ));    
    
    echo view('input/hidden', array(
        'name' => 'theme_id',
        'id' => 'theme_id',
        'value' => $curTheme,
    ));    
?>

<table class='designSettings'>
<tr><th><?php echo __('design:logo'); ?></th>
<td>
<?php   
    echo view("input/image",
        array(
            'jsname' => 'logoUploader',
            'current' => $user->get_icon('medium'),
            'track_dirty' => true,
            'sizes' => User::get_icon_sizes(),
            'removable' => $user->has_custom_icon(),
            'thumbnail_size' => 'medium',
            'name' => 'icon',
            'deletename' => 'deleteicon',
        ))

    ?>    
    <script type='text/javascript'>
    logoUploader.onComplete = function($files) {   
        designSettings.logo = uploader.getFileByProp($files, 'size', 'medium') || $files[0];
        updatePreview();
    };    
    </script>
</td>
</tr>

<tr id='text_header'>
<th style='padding-top:5px'><?php echo __('design:header_text'); ?></th>
<td>
<?php

echo "<table id='heading' style='width:100%'><tr>";            
echo "<td>";
echo "<div style='font-weight:bold;font-size:20px'>".escape($user->name); 
echo " <a style='font-weight:normal;font-size:12px' href='{$user->get_url()}/settings'>".__('design:edit_name')."...</a>";
echo "</div>";
echo view('input/text', array(
    'id' => 'tagline',
    'name' => 'tagline', 
    'attrs' => array(
        'onchange' => 'taglineChanged()'
    ),
    'style' => "width:300px;margin-top:5px;",
    'value' => $user->get_design_setting('tagline')
));
echo "<br />";
echo "<a style='font-weight:normal;font-size:12px' href='javascript:void(0)' id='custom_header_link' onclick='showImageHeader(true)'>".__('design:use_header_image')."...</a>";
echo "</td>";    
echo "</tr></table>";

?>
</td></tr>

<tr id='image_header' style='display:none'>
<th><?php echo __('design:header_image'); ?></th>
<td>
<?php
        echo __('design:header:chooseimage');
         
        echo view("input/image_uploader", array(
            'jsname' => 'headerUploader',
            'track_dirty' => true,
            'sizes' => array('small' => '350x75', 'large' => '700x150',),
            'thumbnail_size' => 'small',
            'name' => 'header_image',
        ));
                
        echo "<div class='help' style='font-size:12px'>";
        echo __('design:header:constraints');
        echo "</div>";
        
        echo "<a style='font-weight:normal;font-size:12px' href='javascript:void(0)' id='default_header_link' onclick='showImageHeader(false)'>".__('design:use_header_text')."...</a>";
?>
<script type='text/javascript'>
    headerUploader.onComplete = function($files) {   
        designSettings.header_image = uploader.getFileByProp($files, 'size', 'large') || $files[0];
        designSettings.custom_header = true;
        updatePreview();
    };    
</script>

</td>
</tr>

<tr>
<th>
<?php echo __('design:theme') ?>
</th>
<td>
<?php       
    $themes = Theme_UserSite::get_available_themes();
    
    $thumbnailWidth = 120;        
    $containerWidth = sizeof($themes) * ($thumbnailWidth + 6);
    
    echo "<div id='themes' style='width:360px;height:170px;overflow:auto;overflow-x:scroll;overflow-y:hidden;margin-bottom:5px;font-size:12px'>";
    echo "<div id='themescroll' style='width:{$containerWidth}px'>";
    
    foreach ($themes as $theme)
    {        
        $id = $theme::get_subtype_id();
    
        echo "<a href='javascript:selectTheme(".json_encode($id).");' onclick='ignoreDirty()' style='text-align:center;display:block;float:left;width:{$thumbnailWidth}px;margin-right:5px'>";
        
        $thumbnail = $theme::get_thumbnail();
        if ($thumbnail)
        {
            echo "<img id='theme_{$id}' src='{$thumbnail}' style='width:110px;height:110px' />";
        }        
        echo "<br />";
        echo escape($theme::get_display_name());        
        echo "</a>";
    }    
    echo "</div>";
    echo "</div>";
    //echo "<a style='font-size:12px' id='showThemeOptions' href='javascript:void(0)' onclick='showThemeOptions()'>Customize theme...</a>";
?>
</td>
</tr>
<th>
&nbsp;
</th>
<td>
<?php
    echo view('input/submit',array(
        'value' => __('savechanges'),
        'track_dirty' => true,
    ));
?>
</td>
</table>

<script type='text/javascript'>
selectTheme(<?php echo json_encode($curTheme) ?>);
showImageHeader(<?php echo json_encode($curCustomHeader); ?>);

</script>
</div>
</div>
</form>
<div style='float:left;width:625px;overflow:hidden'>
<iframe id='preview_frame' src='' style='width:1000px;height:470px'>
</iframe>
</div>

<script type='text/javascript'>

updatePreview();

</script>