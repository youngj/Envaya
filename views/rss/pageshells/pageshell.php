<?php
	header("Content-Type: text/xml");
	
	echo "<?xml version='1.0'?>\n";

	
	
	// Set title
		if (empty($vars['title'])) {
			$title = $vars['config']->sitename;
		} else if (empty($vars['config']->sitename)) {
			$title = $vars['title'];
		} else {
			$title = $vars['config']->sitename . ": " . $vars['title'];
		}
			
		$url = Request::full_original_url();
        $url = str_replace('?view=rss','',$url);
		$url = str_replace('&view=rss','',$url);

?>

<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:georss="http://www.georss.org/georss" <?php echo view('extensions/xmlns'); ?> >
	<channel>
		<title><![CDATA[<?php echo $title; ?>]]></title>
		<link><?php echo htmlentities($url); ?></link>
		<?php echo view('extensions/channel'); ?>
		<?php

			echo $vars['body'];
		
		?>
	</channel>
</rss>