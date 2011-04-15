<?php
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
    
    $addItem("<a href='{$org->get_widget_by_name('home')->get_edit_url()}'>".__('todo:home')."</a>", true);
        
    $contact = $org->get_widget_by_name('contact');       
    $addItem("<a href='{$contact->get_edit_url()}'>".__('todo:contact')."</a>", 
            $contact->time_updated > $contact->time_created && sizeof($org->get_contact_info()) >= 2);

    $history = $org->get_widget_by_name('history');           
    $addItem("<a href='{$history->get_edit_url()}'>".__('todo:history')."</a>",
        $history->is_active() && $history->content
    );            

    $projects = $org->get_widget_by_name('projects');           
    $addItem("<a href='{$projects->get_edit_url()}'>".__('todo:projects')."</a>",
        $projects->is_active() && $projects->content
    );            
    
    $team = $org->get_widget_by_name('team');           
    $addItem("<a href='{$team->get_edit_url()}'>".__('todo:team')."</a>",
        $team->is_active() && $team->content
    );            
    
    $news = $org->get_widget_by_name('news');;
    $numLatestNews = $org->query_news_updates()->where('time_created > ?', time() - 86400 * 31)->count();
    $addItem("<a href='{$news->get_edit_url()}'>".__('todo:news')."</a>",
        $numLatestNews > 0
    );            
    
    $numImages = $org->query_files()->where("size='small'")->count();
    $addItem(
        "<a href='{$org->get_url()}/addphotos'>".__('todo:photos')."</a>",
        $numImages >= 2
    );        
    
    $addItem(
        "<a href='{$org->get_url()}/design'>".__('todo:logo')."</a>",
        ($org->has_custom_header() || $org->has_custom_icon())    
    );    

    $network = $org->get_widget_by_class('WidgetHandler_Network');
    $addItem("<a href='{$network->get_edit_url()}'>".__('todo:network')."</a>",
        $network->is_active()
    );
    
?>
<?php 
if (sizeof($todoItems) && !Session::get('hide_todo'))
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
?>