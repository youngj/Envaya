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
        
        $cancelUrl = get_input('from') ?: $post->get_url();
        PageContext::get_submenu('edit')->add_item(__("canceledit"), $cancelUrl);

        $org = $post->get_container_entity();

        $this->page_draw(array(
            'title' => __('blog:editpost'),
            'content' => view("news/edit_post", array('entity' => $post))
        ));
    }
}    