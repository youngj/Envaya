<?php

class Action_Admin_TranslateContent extends Action
{
    function before()
    {
        $this->require_admin();
    }

    function render()
    {
        $props = get_input_array("prop");
        $from = get_input('from');
        
        $targetLang = get_input('targetlang') ?: Language::get_current_code();

        $content = array();

        foreach ($props as $propStr)
        {
            $guidProp = explode('.', $propStr);
            $guid = $guidProp[0];
            $prop = $guidProp[1];
            $isHTML = (int)$guidProp[2];

            $entity = Entity::get_by_guid($guid);

            if ($entity && $entity->can_edit() && $entity->$prop)
            {
                $content[] = view("translation/translate",
                    array(
                        'entity' => $entity,
                        'property' => $prop,
                        'targetLang' => $targetLang,
                        'isHTML' => $isHTML,
                        'from' => $from));
            }
        }

        $this->page_draw(array(
            'title' => __('trans:title'),
            'theme_name' => 'simple_wide',
            'content' => implode("<hr><br>", $content)
        ));
    }

    function process_input()
    {
        $text = get_input('translation');
        $guid = get_input('entity_guid');
        $isHTML = (int)get_input('html');
        $property = get_input('property');
        $entity = Entity::get_by_guid($guid);

        $origLang = $entity->get_language();

        $actualOrigLang = get_input('language');
        $newLang = get_input('newLang');

        if ($actualOrigLang != $origLang)
        {
            $entity->language = $actualOrigLang;
            $entity->save();
        }
        if ($actualOrigLang != $newLang)
        {
            $trans = $entity->lookup_translation($property, $actualOrigLang, $newLang, TranslateMode::ManualOnly, $isHTML);
            
            if (get_input('delete'))
            {
                $trans->delete();
            }
            else
            {                
                $trans->html = $isHTML;
                $trans->owner_guid = Session::get_loggedin_userid();
                $trans->value = $text;
                $trans->save();
            }
        }

        SessionMessages::add(__("itrans:posted"));

        $this->redirect(get_input('from') ?: $entity->get_url());
    }
}
