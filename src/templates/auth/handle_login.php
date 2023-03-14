<?php
// Strict Typing Mode
declare(strict_types = 1);
// Import Root Constants, etc.
include_once(__DIR__.'/../../config/config.php');
?>

<?php
require_once(SITE_ROOT.'/modules/db/use_db.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form
    $user_name = $_POST['user_name'];
    $user_id = $_POST['user_id'];
    if (auth_login($user_name, $user_id)) { // Success!
        header("Location: {$_SERVER["HTTP_REFERER"]}");
    }
} else { // Problem!
    // TODO: Confirm what to do in this case
    echo "<h3>Invalid login</h3>";
}

?>