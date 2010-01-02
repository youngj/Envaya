<html>

<body>

<h1>Anvaya</h1>

<?php
	$users = get_entities("group","",0,10,false);

	echo elgg_view('extensions/entity_list',array(
		'entities' => $users
    ));

?>

</body>
</html>