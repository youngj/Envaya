<?php
    $widget = $vars['widget'];
    
    $user = Session::get_loggedin_user();

    ob_start();
    
    echo "<p>";
    echo "Now you can publish your organization's news to Envaya from your phone via SMS!";
    echo "</p>";
    
    $dialed_phone_number = PhoneNumber::get_dialed_number(Config::get('news_phone_number'), GeoIP::get_country_code());
    
    echo "<p style='text-align:center;font-size:16px'>";
    echo "Text <strong>P <span style='color:#999'>(your message here)</span></strong><br /> to <strong>"
        . $dialed_phone_number . "</strong>";
    echo "</p>";
    
    echo "<p>";
    echo "Standard message and data rates may apply.";
    echo "</p>";
    
    $content = ob_get_clean();    
    
    echo view("section", array(
        'header' => "Publishing News Updates via SMS",
        'content' => $content
    ));    
?>
