<?php
    echo view('input/tinymce', array(
        'name' => $vars['name'], 
        'value' => $vars['value'], 
        'height' => 396, 
        'width' => 488,
        'track_dirty' => true,
        'saveDraft' => true,
        'entity' => $vars['translation'],
        'save_draft_url' => $vars['base_url'] . '/save_draft',
    ));