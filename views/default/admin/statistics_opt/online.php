
<div class="admin_users_online">
    <h3><?php echo __('admin:statistics:label:onlineusers'); ?></h3>
    <?php
        $offset = get_input('offset',0);
        $limit = 10;
        
        $time = timestamp() - 600;
        $query = User::query()
            ->where('last_action >= ?', $time)
            ->order_by('last_action')
            ->limit($limit, $offset);        
        
        $users = $query->filter();

        if ($users)
        {
            echo view('paged_list', array(
                'items' => array_map(function($user) { 
                    return "<a href='{$user->get_url()}'>".escape($user->name)."</a>";
                }, $users),
                'separator' => '<br />',
                'count' => $query->count(),
                'offset' => $offset,
                'limit' => $limit,
            ));                    
        }    
    ?>
</div>
