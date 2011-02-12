<?php
	header("Content-Type: text/xml");
	
	echo "<?xml version='1.0'?>\n";
	
	// Set title
        $sitename = Config::get('sitename');
    
		if (empty($vars['title'])) {
			$title = $sitename;
		} else if (empty($sitename)) {
			$title = $vars['title'];
		} else {
			$title = $sitename . ": " . $vars['title'];
		}
			
		$url = Request::full_original_url();
        $url = str_replace('?view=rss','',$url);
		$url = str_replace('&view=rss','',$url);

?>

<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:georss="http://www.georss.org/georss">
	<channel>
		<title><![CDATA[<?php echo $title; ?>]]></title>
		<link><?php echo htmlentities($url); ?></link>
		<?php 
			echo $vars['body'];	
		?>
	</channel>
</rss>