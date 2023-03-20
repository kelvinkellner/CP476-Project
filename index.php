<?php
include_once(__DIR__.'/src/templates/head.php');
?>

<?php
// Log the use out if logout button is pressed
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['logout']))
    session_unset();
?>

<?php
// Display different content depending on if user is logged in
if (array_key_exists('user', $_SESSION)) {
    if ($_SESSION['user']['is_admin'])
        echo "Logged in as: ".$_SESSION['user']['user_name'].' ('.$_SESSION['user']['user_id'].' - Admin)';
    else
        echo "Logged in as: ".$_SESSION['user']['user_name'].' ('.$_SESSION['user']['user_id'].')';
    include(__DIR__.'/src/auth/logout_button.php');
    echo "<br>";
    include_once(__DIR__.'/src/table_viewer/container.php');
} else {
    include_once(__DIR__.'/src/auth/login.php');
}
?>

<?php
include_once(__DIR__.'/src/templates/footer.php');
?>
