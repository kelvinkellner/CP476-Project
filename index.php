<?php
include_once(__DIR__.'/src/templates/head.php');
?>

<?php
// Log the use out if logout button is pressed
if($_SERVER['REQUEST_METHOD'])
    if(isset($_POST['logout']))
        session_unset();
    if(isset($_POST['reset_everything'])) {
        include_once(__DIR__.'/src/db/init_db.php');
        reset_db();
        $success = session_unset();
        if ($success)
            echo '<h3>Everything has been reset!</h3><br/>';
        else
            echo '<h3>Something went wrong...</h3><br/>';
    }
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
    echo '<br/><br/><br/><br/><br/>';
    echo '<h3>Need to restart everything for testing purposes???</h3>';
    echo '<p>Click the button below to reset the database to default and delete all session variables.</p>';
    echo '<form id="reset_everything" method="post">';
    echo '<input type="submit" name="reset_everything" value="Reset Absolutely Everything">';
    echo '</form>';
    echo '<p><b>WARNING:</b> There is no way to automatically bring back this data once it is deletes.</p>';
}
?>



<?php
include_once(__DIR__.'/src/templates/footer.php');
?>
