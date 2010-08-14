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
    
    $addItem("<a href='{$org->getWidgetByName('home')->getEditURL()}'>Create home page</a>", true);
    
    $contact = $org->getWidgetByName('contact');       
    $addItem("<a href='{$contact->getEditURL()}'>Add contact information</a>", 
            $contact->time_updated > $contact->time_created && sizeof($org->getContactInfo()) >= 2);

    $history = $org->getWidgetByName('history');           
    $addItem("<a href='{$history->getEditURL()}'>Write about your history</a>",
        $history->isActive() && $history->content
    );            

    $projects = $org->getWidgetByName('projects');           
    $addItem("<a href='{$projects->getEditURL()}'>Write about your projects</a>",
        $projects->isActive() && $projects->content
    );            
    
    $team = $org->getWidgetByName('team');           
    $addItem("<a href='{$team->getEditURL()}'>Add your team members</a>",
        $team->isActive() && $team->content
    );            
    
    $news = $org->getWidgetByName('news');;
    $numLatestNews = $org->queryNewsUpdates()->where('time_created > ?', time() - 86400 * 31)->count();
    $addItem("<a href='{$news->getEditURL()}'>Share your latest news</a>",
        $numLatestNews > 0
    );            
    
    $numImages = $org->queryFiles()->where("size='small'")->count();
    $addItem(
        "<a href='{$org->getURL()}/addphotos'>Add some photos</a>",
        $numImages >= 2
    );        
    
    $addItem(
        "<a href='{$org->getURL()}/design'>Add your logo</a>",
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
There's still more to do! Click the links below to improve your organization's website. 
If you complete all the items below, your website may be eligible to be considered for a 
Featured Organization award. 
</p>
<table>
<tr>
<th>To Do</th>
<th>Done!</th>
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