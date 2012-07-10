<?php
    $user = $vars['user'];
    ob_start();
?>
    <div class='section_content padded'>
    <table class='inputTable'>
    <tr>
        <th><?php echo __('username'); ?>:</th>
        <td>
            <?php echo $user->username ?> <a href='<?php echo $user->get_url() ?>/username'><?php echo __('user:username:change'); ?></a>
        </td>
    </tr>    
    <tr>
        <th><?php echo __('password'); ?>:</th>
        <td>
            <span style='font-family:serif'>********</span> <a href='<?php echo $user->get_url() ?>/password'><?php echo __('user:password:change'); ?></a>
        </td>
    </tr>          
    <tr>
    <th><?php echo __('user:name:label'); ?>:</th>
    <td>
        <?php
            echo view('input/text',array('name' => 'name', 'track_dirty' => true, 'value' => $user->name));
        ?>
    </td>
    </tr>    
    <tr>
        <th><?php echo __('email'); ?>:</th>
        <td>
        <?php
            echo view('input/email',array(
                'name' => 'email', 
                'value' => $user->email, 
                'track_dirty' => true,
            ));
        ?>
        </td>
    </tr>
    <tr>
        <th><?php echo __('phone_number'); ?>:</th>
        <td>
        <?php
            echo view('input/text',array(
                'name' => 'phone', 
                'value' => $user->phone_number, 
                'track_dirty' => true
            ));
        ?>
        </td>
    </tr>
<?php 
    if ($user->country)
    {
?>
    <tr>
        <th><?php echo __('register:city'); ?></th>
        <td style='vertical-align:middle'>
        <?php 
            echo view('input/text', array(
                'name' => 'city',
                'style' => 'width:200px',
                'value' => $user->city,
            )); 
        ?>, <?php echo escape(Geography::get_country_name($user->country)); ?>
        </td>
    </tr>
    <tr>
        <th><?php echo __('register:region'); ?></th>
        <td>
            <?php echo view('input/pulldown', array(
                'name' => 'region',
                'value' => $user->region,
                'options' => Geography::get_region_options($user->country),
                'empty_option' => __('register:region:blank'),
            )) ?>
        </td>
    </tr>
<?php
    }
?>    
    <tr>
        <th><?php echo __('user:language:label'); ?>:</th>
        <td style='padding-top:8px'>
        <?php
            $value = $user->language ?: Config::get('language');

            echo view("input/pulldown", array('name' => 'language', 'value' => $value, 'options' => Language::get_options()));

         ?>
        </td>
    </tr>
    </table>
    <?php

    echo view('input/hidden', array('name' => 'from', 'value' => get_input('from')));
    
    if (Permission_UseAdminTools::has_for_entity($user))
    {   
        echo view('input/alt_submit', array(
            'name' => 'delete', 
            'id' => 'widget_delete', 
            'confirm' => __('areyousure'),
            'value' => __('user:delete'))); 
    }
    
    echo view('input/submit', array('value' => __('savechanges'), 'track_dirty' => true)); 

?>
</div>

<?php 
    $form_body = ob_get_clean();
    
    echo view('input/form',
        array('enctype' => 'multipart/form-data', 'action' => secure_url("{$user->get_url()}/settings", Request::get_host()),
        'body' => $form_body));
?>