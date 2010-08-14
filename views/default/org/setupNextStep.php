<?php
    $org = $vars['entity'];    
   
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
    
    $addItem("<a href='{$org->getWidgetByName('home')->getEditURL()}'>".__('todo:home')."</a>", true);
    
    $contact = $org->getWidgetByName('contact');       
    $addItem("<a href='{$contact->getEditURL()}'>".__('todo:contact')."</a>", 
            $contact->time_updated > $contact->time_created && sizeof($org->getContactInfo()) >= 2);

    $history = $org->getWidgetByName('history');           
    $addItem("<a href='{$history->getEditURL()}'>".__('todo:history')."</a>",
        $history->isActive() && $history->content
    );            

    $projects = $org->getWidgetByName('projects');           
    $addItem("<a href='{$projects->getEditURL()}'>".__('todo:projects')."</a>",
        $projects->isActive() && $projects->content
    );            
    
    $team = $org->getWidgetByName('team');           
    $addItem("<a href='{$team->getEditURL()}'>".__('todo:team')."</a>",
        $team->isActive() && $team->content
    );            
    
    $news = $org->getWidgetByName('news');;
    $numLatestNews = $org->queryNewsUpdates()->where('time_created > ?', time() - 86400 * 31)->count();
    $addItem("<a href='{$news->getEditURL()}'>".__('todo:news')."</a>",
        $numLatestNews > 0
    );            
    
    $numImages = $org->queryFiles()->where("size='small'")->count();
    $addItem(
        "<a href='{$org->getURL()}/addphotos'>".__('todo:photos')."</a>",
        $numImages >= 2
    );        
    
    $addItem(
        "<a href='{$org->getURL()}/design'>".__('todo:logo')."</a>",
        ($org->custom_header || $org->custom_icon)    
    );    
?>
<?php 
if (sizeof($todoItems))
{
?>
<div class='todo_container'>
<div class='good_messages'>
<?php
foreach (system_messages() as $message)
{
    echo "<p><strong>".view('output/longtext',array('value' => $message))."</strong></p>";
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
<ul>
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