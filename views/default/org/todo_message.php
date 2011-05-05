<?php
if (!Session::get('hide_todo'))
{        
    $org = $vars['org'];    
   
    $todoItems = array();
    $doneItems = array();    
   
    $addItem = function($item, $done) use (&$todoItems, &$doneItems)
    {
        if ($done)
        {
            $doneItems[] = $item;
        }
        else
        {
            $todoItems[] = $item;            
        }
    };    
    
    $widget_names = array('home', 'contact','history','projects','team','news','network');
    $query = $org->query_widgets()
        ->where_in('widget_name', $widget_names)
        ->columns('guid, status, time_created, time_updated, container_guid, owner_guid,
                    widget_name, subclass, title, length(content) as content_len');
    
    $widgets = $query->filter();
    
    $widgets_map = array();
    foreach ($widgets as $widget)
    {
        $widgets_map[$widget->widget_name] = $widget;
    }
    foreach ($widget_names as $widget_name)
    {    
        if (!isset($widgets_map[$widget_name]))
        {
            $widgets_map[$widget_name] = $org->new_widget_by_name($widget_name);
        }
    }
        
    $home = $widgets_map['home'];
    $addItem("<a href='{$home->get_edit_url()}'>".__('todo:home')."</a>", true);
    
    $contact = $widgets_map['contact'];
    $addItem("<a href='{$contact->get_edit_url()}'>".__('todo:contact')."</a>", 
            $contact->time_updated > $contact->time_created && sizeof($org->get_contact_info()) >= 2);

    $history = $widgets_map['history'];
    $addItem("<a href='{$history->get_edit_url()}'>".__('todo:history')."</a>",
        $history->is_enabled() && $history->content_len > 0
    );            

    $projects = $widgets_map['projects'];
    $addItem("<a href='{$projects->get_edit_url()}'>".__('todo:projects')."</a>",
        $projects->is_enabled() && $projects->content_len > 0
    );            
    
    $team = $widgets_map['team'];
    $addItem("<a href='{$team->get_edit_url()}'>".__('todo:team')."</a>",
        $team->is_enabled() && $team->content_len > 0
    );            
    
    $news = $widgets_map['news'];
    $hasRecentNews = $news->query_widgets()->where('time_created > ?', time() - 86400 * 31)->exists();
    $addItem("<a href='{$news->get_edit_url()}'>".__('todo:news')."</a>",
        $hasRecentNews > 0
    );            
    
    $numImages = $org->query_files()->where("size='small'")->count();
    $addItem(
        "<a href='{$org->get_url()}/addphotos'>".__('todo:photos')."</a>",
        $numImages >= 2
    );        
    
    $addItem(
        "<a href='{$org->get_url()}/design'>".__('todo:logo')."</a>",
        ($org->get_design_setting('header_image') || $org->has_custom_icon())    
    );    

    $network = $widgets_map['network'];
    $addItem("<a href='{$network->get_edit_url()}'>".__('todo:network')."</a>",
        $network->is_enabled()
    );
    
?>
<?php 
if (sizeof($todoItems))
{
?>
<script type='text/javascript'>
function hideTodo()
{
    hideMessages('todo_container');
    fetchJson("/pg/hide_todo", function(){});
}
</script>
<div class='todo_container' id='todo_container'>
<div class='good_messages'>
<a class='hideMessages' style='margin-right:-5px;margin-top:-10px;' href='javascript:hideTodo()' onclick='ignoreDirty()'></a>
<?php

$messages = SessionMessages::get_register('messages');
if ($messages)
{
    foreach ($messages as $message)
    {
        echo "<p><strong>$message</strong></p>";
    }
}
?>
<p>
<?php echo __('todo:about'); ?> 
</p>
<table>
<tr>
<th><?php echo __('todo:todo') ?></th>
<th><?php echo __('todo:done') ?></th>
</tr>
<tr>
<td>
<ul class='todo_steps'>
<?php
foreach ($todoItems as $todoItem)
{
    echo "<li>$todoItem</li>";
}
?>
</ul>
</td>
<td>
<ul class='done_steps'>
<?php 
foreach ($doneItems as $doneItem)
{
    echo "<li>$doneItem</li>";
}
?>
</ul>
</td>
</tr>
</table>

</div>
</div>
<?php 
}
}
?>