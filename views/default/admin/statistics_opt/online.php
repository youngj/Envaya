
<div class="admin_users_online">
    <h3><?php echo __('admin:statistics:label:onlineusers'); ?></h3>
    <?php
        $offset = get_input('offset',0);
        $limit = 10;
        
        $time = time() - 600;
        $query = User::query()->where('last_action >= ?', $time)->order_by('last_action')->limit($limit, $offset);
        
        $objects = $query->filter();

        if ($objects)
        {
            echo view_entity_list($objects, $query->count(),$offset, $limit);
        }    
    ?>
</div>
