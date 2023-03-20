<?php
include_once(__DIR__.'/src/templates/head.php');
?>

<?php
// echo session_unset(); // TODO: remove eventually
if (array_key_exists('user', $_SESSION)) {
    echo '<br>';
    echo $_SESSION['user']['user_name'].'<br>';
    echo $_SESSION['user']['user_id'].'<br>';
    echo $_SESSION['user']['is_admin'].'<br>';
} else {
    include_once(__DIR__.'/src/auth/login.php');
}
?>

<?php
include_once(__DIR__.'/src/templates/footer.php');
?>
