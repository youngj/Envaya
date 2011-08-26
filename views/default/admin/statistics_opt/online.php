
<div class="admin_users_online">
    <h3><?php echo __('admin:statistics:label:onlineusers'); ?></h3>
    <?php
        $offset = get_input('offset',0);
        $limit = 10;
        
        $time = timestamp() - 600;
        $query = User::query()->where('last_action >= ?', $time)->order_by('last_action')->limit($limit, $offset);
        
        $objects = $query->filter();

        if ($objects)
        {
            echo view('paged_list', array(
                'entities' => $objects,
                'count' => $query->count(),
                'offset' => $offset,
                'limit' => $limit,
            ));                    
        }    
    ?>
</div>
