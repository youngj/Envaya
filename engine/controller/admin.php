<?php

class Controller_Admin extends Controller
{
    function before()
    {
        admin_gatekeeper();
        set_context('editor');
        set_theme('editor');

    }

    function action_contact()
    {
        $title = elgg_echo('email:send');
        $area1 = elgg_view('admin/contact');
        $body = elgg_view_layout("one_column_wide", elgg_view_title($title), $area1);
        $this->page_draw($title,$body);
    }


    function action_confirm_email()
    {
        $title = elgg_echo('email:send');
        $org = get_user_by_username(get_input('username'));

        if ($org)
        {
            $area1 = elgg_view('admin/sendEmail', array('org' => $org, 'from' => get_input('from')));

            $body = elgg_view_layout("one_column", elgg_view_title($title), $area1);

            $this->page_draw($title,$body);
        }
        else
        {
            not_found();
        }
    }

    function action_view_email()
    {
        $user = get_user_by_username(get_input('username') ?: 'envaya');

        if ($user)
        {
            echo elgg_view('emails/reminder', array('org' => $user));
        }
        else
        {
            not_found();
        }
    }

    function action_send_email()
    {
        action_gatekeeper();

        /*
        $orgs = Organization::filterByCondition(
            array("approval > 0 AND notify_days > 0 AND ((last_notify_time IS NULL) OR (last_notify_time + notify_days * 86400 < ?)) AND email <> ''"),
            array($time), '', 1);
        */

        global $CONFIG;

        $org = get_entity(get_input('org_guid'));

        if ($org && $org->email && $org->notify_days > 0 && $org->approval > 0
            && (!$org->last_notify_time || $org->last_notify_time + $org->notify_days * 86400 < time())
            )
        {
            $subject = elgg_echo('email:reminder:subject', $org->language);

            $body = elgg_view('emails/reminder', array('org' => $org));

            $headers = array(
                'To' => $org->getNameForEmail(),
                'Content-Type' => 'text/html'
            );

            send_mail($org->email, $subject, $body, $headers);

            $org->last_notify_time = time();
            $org->save();

            system_message(elgg_echo('email:reminder:sent'));
        }
        else
        {
            register_error(elgg_echo('email:reminder:none'));
        }

        forward(get_input('from') ?: "/admin/contact");
    }

    function action_translateQueue()
    {
        $title = elgg_echo('translate:queue');

        $body = elgg_view_layout("one_column_padded", elgg_view_title($title),
            elgg_view('translate/queue', array('lang' => get_language()))
        );

        $this->page_draw($title,$body);
    }


    function action_statistics()
    {
        $title = elgg_echo("admin:statistics");
        $this->page_draw($title,
            elgg_view_layout("one_column_padded", elgg_view_title($title), elgg_view("admin/statistics")));
    }

    function action_user()
    {
        $search = get_input('s');
        $limit = get_input('limit', 10);
        $offset = get_input('offset', 0);

        $title = elgg_view_title(elgg_echo('admin:user'));

        $result = list_entities('user', '', 0, $limit, false);

        $this->page_draw(
            elgg_echo("admin:user"),
            elgg_view_layout("one_column_padded", $title,  elgg_view("admin/user") . $result)
        );
    }

    function action_search()
    {
        // Get input
        $tag = stripslashes(get_input('tag'));
        $subtype = stripslashes(get_input('subtype'));
        if (!$objecttype = stripslashes(get_input('object'))) {
            $objecttype = "";
        }
        if (!$md_type = stripslashes(get_input('tagtype'))) {
            $md_type = "";
        }
        $owner_guid = (int)get_input('owner_guid',0);
        if (substr_count($owner_guid,',')) {
            $owner_guid_array = explode(",",$owner_guid);
        } else {
            $owner_guid_array = $owner_guid;
        }

        if (empty($objecttype) && empty($subtype)) {
            $title = sprintf(elgg_echo('search:title_with_query'),$tag);
        } else {
            if (empty($objecttype)) $objecttype = 'object';
            $itemtitle = 'item:' . $objecttype;
            if (!empty($subtype)) $itemtitle .= ':' . $subtype;
            $itemtitle = elgg_echo($itemtitle);
            $title = sprintf(elgg_echo('advancedsearchtitle'),$itemtitle,$tag);
        }

        if (!empty($tag)) {
            $body = "";
            $body .= elgg_view_title($title); // elgg_view_title(sprintf(elgg_echo('search:title_with_query'),$tag));
            $body .= trigger_plugin_hook('search','',$tag,"");
            $body = elgg_view_layout('one_column_padded','',$body);
        }

        $this->page_draw($title,$body);

    }

    function action_logbrowser()
    {
        $limit = get_input('limit', 40);
        $offset = get_input('offset');

        $search_username = get_input('search_username');
        if ($search_username) {
            if ($user = get_user_by_username($search_username)) {
                $user = $user->guid;
            }
        } else {
            $user_guid = get_input('user_guid',0);
            if ($user_guid) {
                $user = (int) $user_guid;
            } else {
                $user = "";
            }
        }

        $timelower = get_input('timelower');
        if ($timelower) $timelower = strtotime($timelower);
        $timeupper = get_input('timeupper');
        if ($timeupper) $timeupper = strtotime($timeupper);

        $title = elgg_view_title(elgg_echo('logbrowser'));

        // Get log entries
        $log = get_system_log($user, "", "", "","", $limit, $offset, false, $timeupper, $timelower);
        $count = get_system_log($user, "", "", "","", $limit, $offset, true, $timeupper, $timelower);
        $log_entries = array();

        foreach ($log as $l)
        {
            $tmp = new ElggObject();
            $tmp->subtype = T_logwrapper;
            $tmp->entry = $l;
            $log_entries[] = $tmp;
        }

        $form = elgg_view('logbrowser/form',array('user_guid' => $user, 'timeupper' => $timeupper, 'timelower' => $timelower));

        $result = elgg_view_entity_list($log_entries, $count, $offset, $limit, false, false);


        $this->page_draw(elgg_echo('logbrowser'),elgg_view_layout("one_column_padded", $title,  $form . $result));

    }

    function action_add_user()
    {
        action_gatekeeper();

        // Get variables
        global $CONFIG;
        $username = get_input('username');
        $password = get_input('password');
        $password2 = get_input('password2');
        $email = get_input('email');
        $name = get_input('name');

        $admin = get_input('admin');
        if (is_array($admin)) $admin = $admin[0];

        // For now, just try and register the user
        try {
            if (
                (
                    (trim($password)!="") &&
                    (strcmp($password, $password2)==0)
                ) &&
                ($guid = register_user($username, $password, $name, $email, true))
            ) {
                $new_user = get_entity($guid);
                if (($guid) && ($admin != null))
                {
                    $new_user->admin = true;
                }

                $new_user->admin_created = true;
                $new_user->created_by_guid = get_loggedin_userid();
                $new_user->save();

                notify_user($new_user->guid, $CONFIG->site_guid, elgg_echo('useradd:subject'), sprintf(elgg_echo('useradd:body'), $name, $CONFIG->sitename, $CONFIG->url, $username, $password));

                system_message(sprintf(elgg_echo("adduser:ok"),$CONFIG->sitename));
            } else {
                action_error(elgg_echo("adduser:bad"));
            }
        } catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }

        forward_to_referrer();
    }

    function action_approve()
    {
        action_gatekeeper();

        $guid = (int)get_input('org_guid');
        $entity = get_entity($guid);

        if (($entity) && ($entity instanceof Organization))
        {
            $approvedBefore = $entity->isApproved();

            $entity->approval = (int)get_input('approval');

            $approvedAfter = $entity->isApproved();

            $entity->save();

            if (!$approvedBefore && $approvedAfter)
            {
                notify_user($entity->guid, $CONFIG->site_guid,
                    elgg_echo('email:orgapproved:subject', $entity->language),
                    sprintf(elgg_echo('email:orgapproved:body', $entity->language),
                        $entity->name,
                        $entity->getURL(),
                        "{$CONFIG->url}pg/dashboard",
                        elgg_echo('help:title', $entity->language),
                        "{$CONFIG->url}org/help"
                    ),
                    NULL, 'email');
            }

            system_message(elgg_echo('approval:changed'));
        }
        else
        {
            register_error(elgg_echo('approval:notapproved'));
        }

        forward($entity->getUrl());

    }

    function action_delete_entity()
    {
        action_gatekeeper();

        $guid = get_input('guid');

        $entity = get_entity($guid);

        if (($entity) && ($entity->canEdit()))
        {
            if ($entity->delete())
                system_message(sprintf(elgg_echo('entity:delete:success'), $guid));
            else
                register_error(sprintf(elgg_echo('entity:delete:fail'), $guid));
        }
        else
            register_error(sprintf(elgg_echo('entity:delete:fail'), $guid));

        forward('pg/admin/user');
    }
}