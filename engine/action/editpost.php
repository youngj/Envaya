<?php

class Action_EditPost extends Action
{
    function before()
    {
        $this->require_editor();
    }

    function process_input()
    {        
        $this->validate_security_token();
        $post = $this->get_post();
        $org = $this->get_org();

        $body = get_input('blogbody');

        if (get_input('delete'))
        {
            $org = $post->get_container_entity();
            $post->disable();
            $post->save();
            system_message(__('blog:delete:success'));
            forward($org->get_url()."/news");
        }
        else if (empty($body))
        {
            register_error(__("blog:blank"));
            return $this->render();
        }
        else
        {
            $post->set_content($body);
            $post->save();

            system_message(__("blog:updated"));
            forward($post->get_url());
        }
    }
    
    function render()
    {
        $post = $this->get_post();
        
        $title = __('blog:editpost');

        $cancelUrl = get_input('from') ?: $post->get_url();

        PageContext::get_submenu('edit')->add_item(__("canceledit"), $cancelUrl);

        $org = $post->get_container_entity();
        $area1 = view("org/editPost", array('entity' => $post));
        $body = view_layout("one_column_padded", view_title($title), $area1);

        $this->page_draw($title,$body);
    }
}    