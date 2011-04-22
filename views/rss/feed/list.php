<?php
    foreach ($vars['items'] as $feedItem)
    {
        if ($feedItem->is_valid())
        {
            echo view('feed/item', array('item' => $feedItem));
        }
    }