<?php

require_once "scripts/cmdline.php";
require_once "engine/start.php";

Config::set('debug', false);

function listRedirects()
{
    $redirects = NotFoundRedirect::all();
    
    if (sizeof($redirects) > 0)
    {
        foreach ($redirects as $redirect)
        {
            echo "$redirect\n";
        }
    }
    else
    {
        echo "no redirects found\n";
    }
}

function testRedirect($url)
{
    $redirect_url = NotFoundRedirect::get_redirect_url($url);
    if ($redirect_url)
    {
        echo "$url -> $redirect_url\n";
    }
    else
    {
        echo "'$url' did not match any redirects\n";
    }
}

function deleteRedirect($id)
{
    $redirect = NotFoundRedirect::query()->where('id = ?', $id)->get();
    if ($redirect)
    {
        $redirect->delete();
        echo "redirect deleted: $redirect\n";
    }
    else
    {
        echo "redirect id=$id does not exist\n";
    }
}

function editRedirect($id, $opts)
{
    $redirect = NotFoundRedirect::query()->where('id = ?', $id)->get();
    
    if (!$redirect)
    {
        echo "redirect id=$id does not exist\n";   
    }
    else
    {
        $pattern = @$opts['pattern'];
        $replacement = @$opts['replacement'];
        $order = @$opts['order'];   
            
        if ($pattern)
        {
            $redirect->pattern = $pattern;
        }
        
        if ($replacement)
        {
            $redirect->replacement = $replacement;
        }
        
        if ($order)
        {
            $redirect->order = $order;
        }
        
        try
        {
            $redirect->validate();
        }
        catch (ValidationException $ex)
        {
            echo $ex->getMessage()."\n";
            die;
        }
        $redirect->save();
        
        echo "redirect saved: $redirect\n";
    }
}

function addRedirect($opts)
{
    $pattern = @$opts['pattern'];
    $replacement = @$opts['replacement'];
    $order = @$opts['order'];
    
    if (!$order)
    {
        $max = NotFoundRedirect::query()->order_by('`order` desc')->limit(1)->get();
        if ($max)
        {
            $order = $max->order + 10;
        }
    }
    if (!$pattern)
    {
        echo "missing --pattern\n";
        return usage();
    }
    if (!$replacement)
    {
        echo "missing --replacement\n";
        return usage();
    }     
     
    $redirect = new NotFoundRedirect();
    $redirect->pattern = $pattern;
    $redirect->replacement = $replacement;
    $redirect->order = $order;
    
    try
    {
        $redirect->validate();
    }
    catch (ValidationException $ex)
    {
        echo $ex->getMessage()."\n";
        die;
    }

    $redirect->save();
    
    echo "redirect added: $redirect\n";
}

function usage()
{
    global $argv;
    echo "\nUsage:\n\n";
    echo "List redirects\n";
    echo "-l\n\n";    

    echo "Add redirect\n";
    echo "-a --pattern=<pattern> --replacement=<replacement> [--order=<order>]\n\n";    
    
    echo "Edit redirect\n";
    echo "-e <id> [--pattern=<pattern>] [--replacement=<replacement>] [--order=<order>]\n\n";
       
    echo "Delete redirect\n";
    echo "-d <id>\n\n";
   
    echo "Test redirect\n";
    echo "-t <url>\n\n";   
}

function main()
{        
    $opts = getopt('e:t:ad:lh',array("order:","pattern:","replacement:"));                               
    
    if (isset($opts['t']))
        return testRedirect(@$opts['t']);
    
    if (isset($opts['d']))
        return deleteRedirect(@$opts['d']);
    
    if (isset($opts['e']))
        return editRedirect($opts['e'], $opts);
    
    if (isset($opts['a']))
        return addRedirect($opts);
    
    if (isset($opts['l']))
        return listRedirects();
        
    return usage();
}

main();