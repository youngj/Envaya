<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();

    ob_start();
?>
<div class='section_content padded'>
<?php
    echo view("widgets/edit_content", array('widget' => $widget));
    echo view('widgets/team_add_member', array('widget' => $widget));
?>
</div>
<?php
    $content = ob_get_clean();

    echo view("widgets/edit_form", array(
            'widget' => $widget,
            'body' => $content
    ));
?>