<?php
    $user = $vars['user'];

    $curThemeId = Input::restore_value('theme_id', $user->get_design_setting('theme_id'));    
    
    $curTheme = Theme::get_class($curThemeId);
    
    $curCustomHeader = Input::restore_value('custom_header', $user->get_design_setting('custom_header')) ? 1 : 0;
    
    $preview_url = $user->get_url();
    
    $preview_url = url_with_param($preview_url, '__topbar', '0');
    
    PageContext::add_header_html("
    <style type='text/css'>
    .thin_column { width: 1100px; }
    </style>
    ");    
    
    $theme_options = [];
    
    foreach (Theme_UserSite::get_available_themes() as $theme)
    {        
        $subtype_id = $theme::get_subtype_id();
    
        $theme_options[$subtype_id] = [
            'subtype_id' => $subtype_id,
            'thumbnail' => $theme::$thumbnail,
            'name' => $theme::get_display_name(),
            'layout' => $theme::$layout,
        ];
    }
    
    PageContext::add_js_string('design:layout:default');
    PageContext::add_js_string('design:layout:two_column_left_sidebar');

    echo view('js/json');    
    echo view('js/create_modal_box');    
?>

<script type='text/javascript'>

var designSettings = <?php echo json_encode($user->get_design_settings()); ?>;
var ThemeOptions = <?php echo json_encode($theme_options); ?>;

function updatePreview()
{
    var url = urlWithParam(<?php echo json_encode($preview_url); ?>, '__preview',  encodeURIComponent(JSON.serialize(designSettings)));

    var previewFrame = $('preview_frame');
    if (previewFrame)
    {
        previewFrame.src = url;
    }
}

function showImageHeader(show)
{
    $('text_header').style.display = show ? 'none' : '';
    $('image_header').style.display = show ? '' : 'none';
    
    $('custom_header').value = show ? '1' : '0';
    
    designSettings.custom_header = show;
    updatePreview();    
}

function makeThemeContainer(theme)
{
    var isSelected = designSettings.theme_id == theme.subtype_id;

    return createElem('div', {className:'float_left', style: {width:'110px', height:'140px',textAlign:'center'}},
        createElem('a', {
            href:'javascript:void(0)',
            click: function(e) { 
                selectTheme(theme.subtype_id);
                removeElem(window.themeBox);                
                e.returnValue = false;
            }
        }, 
        createElem('img', {style: {width:'100px',height:'100px', border: isSelected ? '3px solid #069' : '3px solid #fff'}, src: theme.thumbnail}),
        theme.name)
    );
}

function getLayoutName(theme)
{
    return __('design:layout:' + theme.layout.replace('layouts/',''));
}

function chooseTheme()
{
    var content = createElem('div', {className:'modalBody'});
    
    var layouts = {};
    
    for (var subtypeId in ThemeOptions)
    {
        var theme = ThemeOptions[subtypeId];
        var layoutContainer = layouts[theme.layout];
        if (!layoutContainer)
        {
            layoutContainer = createElem('div', {style: {clear:'both'}}, createElem('h3', getLayoutName(theme)));
            content.appendChild(layoutContainer);
            layouts[theme.layout] = layoutContainer;
        }
    
        layoutContainer.appendChild(makeThemeContainer(theme));
    }

    content.appendChild(createElem('div', {style:{clear:'both'}}));
    
    window.themeBox = createModalBox({
        title: 'Change Theme',
        width: 920,
        top:20,
        hideOk: true,
        shadowCancel: true,
        content: content
    });
    
    document.body.appendChild(window.themeBox);
}

function selectTheme(themeId)
{
    setDirty(true);
    $('theme_id').value = themeId;        
        
    var theme = ThemeOptions[themeId];
    if (theme)
    {    
        $('theme_thumbnail').src = theme.thumbnail;
        setElemText($('theme_name'), theme.name + ' (' + getLayoutName(theme) + ')');
    }
    
    designSettings.theme_id = themeId;
    
    if (window.updatePreview)
    {        
        updatePreview();
    }
}

function taglineChanged()
{
    designSettings.tagline = $('tagline').value;
    updatePreview();
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
        'value' => $curThemeId,
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
    echo "<a style='float:right;font-size:12px' href='javascript:void(0)' onclick='chooseTheme();return false'>".__('design:change_theme')."...</a>";
    echo "<a href='javascript:void(0)' onclick='chooseTheme();return false'>";
    echo "<img id='theme_thumbnail' style='width:150px;height:150px;border:1px solid #ccc' src='' />";
    echo "</a>";
    echo "<br />";
    echo "<a id='theme_name' href='javascript:void(0)' onclick='chooseTheme();return false'></a>";    
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
selectTheme(<?php echo json_encode($curThemeId) ?>);
setDirty(false);
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