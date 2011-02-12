<?php 
    if (Config::get('analytics_enabled')) {
?>    

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("<?php echo Config::get('google_analytics_id'); ?>");
pageTracker._trackPageview();
} catch(err) {}</script>

<noscript>
<img src="<?php echo google_analytics_image_url(); ?>" width='1' height='1' />
</noscript>

<?php 
    }
?>

</body>
</html>