<?php
    echo view('page_elements/doctype');
    $lang = escape(Language::get_current_code());
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang ?>" lang="<?php echo $lang ?>">
<head>
<?php echo view('page_elements/head_content', $vars); ?>
</head>
<body>