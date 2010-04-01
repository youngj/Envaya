<?php

    /**
     * Elgg pageshell
     * The standard HTML header that displays across the site
     * 
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     * 
     * @uses $vars['config'] The site configuration settings, imported
     * @uses $vars['title'] The page title
     * @uses $vars['body'] The main content of the page
     * @uses $vars['messages'] A 2d array of various message registers, passed from system_messages()
     */
     
     // Set title
        if (empty($vars['title'])) {
            $title = $vars['config']->sitename;
        } else if (empty($vars['config']->sitename)) {
            $title = $vars['title'];
        } else {
            $title = $vars['config']->sitename . ": " . $vars['title'];
        }
        
        global $autofeed;
        if (isset($autofeed) && $autofeed == true) {
            $url = $url2 = full_url();
            if (substr_count($url,'?')) {
                $url .= "&view=rss";
            } else {
                $url .= "?view=rss";
            }
            if (substr_count($url2,'?')) {
                $url2 .= "&view=odd";
            } else {
                $url2 .= "?view=opendd";
            }
            $feedref = <<<END
            
    <link rel="alternate" type="application/rss+xml" title="RSS" href="{$url}" />
    <link rel="alternate" type="application/odd+xml" title="OpenDD" href="{$url2}" />
            
END;
        } else {
            $feedref = "";
        }
        
        $cacheVersion = $vars['config']->simplecache_version;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo escape($title); ?></title>
    
    <base href='<?php echo $vars['url'] ?>' />

    <?php 
        $theme = get_theme();
        
        echo  '<link rel="stylesheet" href="_css/'.$theme.'.css?v='.$cacheVersion.'" type="text/css" />';
    
        echo $feedref;
    ?>
    
    <!--[if IE 6]>
    <style type='text/css'>
    .green #site_menu a { width:10px; }
    </style>
    <![endif]-->

    
<script type='text/javascript'>
function addEvent(elem, type, fn) 
{
    if (elem.addEventListener) 
    {
        elem.addEventListener(type, fn, false);
    } 
    else 
    {
        elem.attachEvent('on' + type, fn);
    }    
}    

function removeEvent(elem, type, fn)    
{
    if (elem.removeEventListener) 
    {
        elem.removeEventListener(type, fn, false);
    } 
    else 
    {
        elem.detachEvent('on'+type, fn);
    }        
}    

var _jsonCache = {};

function fetchJson(url, fn)
{
    if (_jsonCache[url])
    {
        setTimeout(function() {
            fn(_jsonCache[url]);
        }, 1);    
        return null;
    }
    else
    {
        var xhr = (window.ActiveXObject && !window.XMLHttpRequest) ? new ActiveXObject("Msxml2.XMLHTTP") : new XMLHttpRequest();
        xhr.onreadystatechange = function() 
        {
            if(xhr.readyState == 4 && xhr.status == 200)
            {            
                var $data;
                eval("$data = " + xhr.responseText);    
                _jsonCache[url] = $data;
                fn($data);
            }
        };
        xhr.open("GET", url, true);
        xhr.send(null);
        return xhr;
    }
}

function bind(obj, fn)
{
    return function() {
        return fn(obj);
    };
}
  
function removeChildren(elem)
{
    while (elem.firstChild)
    {
        elem.removeChild(elem.firstChild);
    }
}


window.dirty = false;
function setDirty($dirty)
{
    if ($dirty)
    {    
        if (!window.onbeforeunload)
        {
            window.onbeforeunload = function() {
                return <?php echo json_encode(elgg_echo("page:dirty")) ?>;
            };
        }    
    }    
    else
    {        
        window.onbeforeunload = null;
    }
    window.dirty = $dirty;
    
    return true;
}

</script>
    
</head>

<body class='<?php echo get_theme(); ?>'>

<?php if (get_input("__readonly") == "1") { ?>
<div style='position:absolute;background-color:white;width:500px;height:500px;left:0px;top:0px;opacity:0.01;z-index:100;filter:alpha(opacity=1);z-index:100'></div>
<?php } ?>