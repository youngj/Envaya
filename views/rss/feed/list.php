<?php
    foreach ($vars['items'] as $feedItem)
    {
        echo view('feed/item', array('item' => $feedItem));
    }