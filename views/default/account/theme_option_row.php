<?php
    $user = $vars['user'];
    $name = $vars['name'];
    $title = $vars['title'];
    
    $value = $user->get_design_setting($name);
?>
<th>
<?php echo escape($title); ?>
</th>
<td style='padding-right:30px'>
<?php 
    echo view('input/hidden', array(
        'id' => "theme_option_$name",
        'name' => "theme_options[$name]",
        'value' => $value,
    ));

    echo "<div id='theme_option_patch_$name'></div>";
?>
<script type='text/javascript'>
(function() {
var container = $('theme_option_patch_<?php echo $name ?>');

addEvent(container, 'click', function() {
    selectThemeOption(<?php echo json_encode($name).",".json_encode($title) ?>, $('theme_option_<?php echo $name ?>').value);
});

container.appendChild(makeThemeOptionPatch(<?php echo json_encode($name) ?>, <?php echo json_encode($value) ?>));
 
})();
</script>
</td>
