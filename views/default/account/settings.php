<?php
    $user = $vars['entity'];
    ob_start();
?>
    <div class='section_content padded'>
    <div class='input'>
        <label><?php echo __('user:name:label'); ?>:</label><br />
        <?php

            echo view('input/text',array('name' => 'name', 'trackDirty' => true, 'value' => $user->name));
        ?>
    </div>

    <div class='input'>
        <label><?php echo __('user:password:label'); ?>:</label><br />
        <?php
            echo view('input/password',array('name' => 'password', 'trackDirty' => true));
        ?>
        <div class='help'><?php echo __('user:password:help'); ?></div>
    </div>
    <div class='input'>
        <label>
        <?php echo __('user:password2:label'); ?>:</label><br /> <?php
            echo view('input/password',array('name' => 'password2', 'trackDirty' => true));
        ?>
    </div>

    <div class='input'>
        <label><?php echo __('email'); ?>:</label><br />
        <?php
            echo view('input/email',array('name' => 'email', 'value' => $user->email, 'trackDirty' => true));
        ?>
    </div>

    <div class='input'>
        <label><?php echo __('phone_number'); ?>:</label><br />
        <?php
            echo view('input/text',array('name' => 'phone', 'value' => $user->phone_number, 'trackDirty' => true));
        ?>
    </div>

    <div class='input'>

        <label><?php echo __('user:language:label'); ?>:</label><br />
        <?php
            $value = Config::get('language');
            if ($user->language)
                $value = $user->language;

            echo view("input/pulldown", array('name' => 'language', 'value' => $value, 'options' => Language::get_options()));

         ?>

    </div>

    <?php if ($user instanceof Organization) { ?>

    <div class='input'>

        <label><?php echo __('user:notification:label'); ?>:</label><br />

        <?php		
            echo view("input/checkboxes", array('name' => 'notifications', 
				'value' => $user->get_notifications(), 
				'options' => Notification::get_options()
            ));           
         ?>

    </div>

    <?php } ?>

    <?php echo view('input/hidden', array('name' => 'from', 'value' => get_input('from'))); ?>

    <?php echo view('input/submit', array('value' => __('savechanges'), 'trackDirty' => true)); ?>

    </div>

<?php 
    $form_body = ob_get_clean();
    
    echo view('input/form',
        array('enctype' => 'multipart/form-data', 'action' => secure_url("{$user->get_url()}/settings"),
        'body' => $form_body));
?>