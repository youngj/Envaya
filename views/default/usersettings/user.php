<?php

    $user = $vars['entity'];

    if ($user) {
?>
    <div class='section_content padded'>
    <div class='input'>
        <label><?php echo __('user:name:label'); ?>:</label><br />
        <?php

            echo view('input/text',array('internalname' => 'name', 'trackDirty' => true, 'value' => $user->name));
        ?>
    </div>

    <div class='input'>
        <label><?php echo __('user:password:label'); ?>:</label><br />
        <?php
            echo view('input/password',array('internalname' => 'password', 'trackDirty' => true));
        ?>
        <div class='help'><?php echo __('user:password:help'); ?></div>
    </div>
    <div class='input'>
        <label>
        <?php echo __('user:password2:label'); ?>:</label><br /> <?php
            echo view('input/password',array('internalname' => 'password2', 'trackDirty' => true));
        ?>
    </div>

    <div class='input'>
        <label><?php echo __('email:address:label'); ?>:</label><br />
        <?php
            echo view('input/email',array('internalname' => 'email', 'value' => $user->email));
        ?>
    </div>

    <div class='input'>
        <label><?php echo __('user:phone:label'); ?>:</label><br />
        <?php
            echo view('input/text',array('internalname' => 'phone', 'value' => $user->phone_number));
        ?>
    </div>

    <div class='input'>

        <label><?php echo __('user:language:label'); ?>:</label><br />
        <?php
            $value = Config::get('language');
            if ($user->language)
                $value = $user->language;

            echo view("input/pulldown", array('internalname' => 'language', 'value' => $value, 'options' => Language::get_options()));

         ?>

    </div>

    <?php if ($user instanceof Organization) { ?>

    <div class='input'>

        <label><?php echo __('user:notification:label'); ?>:</label><br />

        <?php		
            echo view("input/checkboxes", array('internalname' => 'notifications', 
				'value' => $user->get_notifications(), 
				'options' => Notification::get_options()
            ));           
         ?>

    </div>

    <?php } ?>

    <?php echo view('input/hidden', array('internalname' => 'from', 'value' => get_input('from'))); ?>

    <?php echo view('input/submit', array('value' => __('savechanges'), 'trackDirty' => true)); ?>

    </div>


<?php } ?>