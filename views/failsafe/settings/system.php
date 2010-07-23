<?php

    /**
     * Elgg system settings form
     * The form to change system settings
     *
     * @package Elgg
     * @subpackage Core

     * @author Curverider Ltd

     * @link http://elgg.org/
     *
     * @uses $vars['action'] If set, the place to forward the form to (usually action/systemsettings/save)
     */

        global $CONFIG;
    // Set action appropriately
        if (!isset($vars['action'])) {
            $action = $vars['url'] . "action/systemsettings/save";
        } else {
            $action = $vars['action'];
        }

        $form_body = "";
        foreach(array('sitename','sitedescription', 'siteemail', 'url','path','dataroot', 'view') as $field) {
            $form_body .= "<p>";
            $form_body .= __('installation:' . $field) . "<br />";
            $warning = __('installation:warning:' . $field);
            if ($warning != 'installation:warning:' . $field) echo "<b>" . $warning . "</b><br />";
            $value = $vars['config']->$field;
            if ($field == 'view') $value = 'default';
            $form_body .= elgg_view("input/text",array('internalname' => $field, 'value' => $value));
            $form_body .= "</p>";
        }

        $languages = get_installed_translations(true);
        $form_body .= "<p>" . __('installation:language') . elgg_view("input/pulldown", array('internalname' => 'language', 'value' => $vars['config']->language, 'options_values' => $languages)) . "</p>";

        $form_body .= "<p>" . __('installation:sitepermissions') . elgg_view('input/access', array('internalname' => 'default_access','value' => ACCESS_LOGGED_IN)) . "</p>";

        $form_body .= "<p class=\"admin_debug\">" . __('installation:debug') . "<br />" .elgg_view("input/checkboxes", array('options' => array(__('installation:debug:label')), 'internalname' => 'debug', 'value' => ($vars['config']->debug ? __('installation:debug:label') : "") )) . "</p>";

        $form_body .= "<p class=\"admin_debug\">" . __('installation:httpslogin') . "<br />" .elgg_view("input/checkboxes", array('options' => array(__('installation:httpslogin:label')), 'internalname' => 'https_login', 'value' => ($vars['config']->https_login ? __('installation:httpslogin:label') : "") )) . "</p>";

        $form_body .= "<p class=\"admin_debug\">" . __('installation:disableapi') . "<br />";
        $on = __('installation:disableapi:label');
        if ((isset($CONFIG->disable_api)) && ($CONFIG->disable_api == true))
            $on = ($vars['config']->disable_api ?  "" : __('installation:disableapi:label'));
        $form_body .= elgg_view("input/checkboxes", array('options' => array(__('installation:disableapi:label')), 'internalname' => 'api', 'value' => $on ));
        $form_body .= "</p>";

        $form_body .= "<p class=\"admin_usage\">" . __('installation:usage') . "<br />";
        $on = __('installation:usage:label');

        $form_body .= elgg_view("input/checkboxes", array('options' => array(__('installation:usage:label')), 'internalname' => 'usage', 'value' => $on ));
        $form_body .= "</p>";

        $form_body .= elgg_view('input/hidden', array('internalname' => 'settings', 'value' => 'go'));

        $form_body .= elgg_view('input/submit', array('value' => __("save")));

        echo elgg_view('input/form', array('action' => $action, 'body' => $form_body));

?>