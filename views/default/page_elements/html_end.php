<?php
    $analytics_backend = Config::get('analytics:backend');
    if ($analytics_backend == 'Analytics_Google') {
        echo view('page_elements/google_analytics');
    }
?>
</body>
</html>