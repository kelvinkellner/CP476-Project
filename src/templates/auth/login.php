<?php
// Strict Typing Mode
declare(strict_types = 1);
// Import Root Constants, etc.
include_once(__DIR__.'/../../config/config.php');
?>

<?php
require_once(SITE_ROOT.'/modules/db/use_db.php');

if (isset($_SESSION['user_name']) && isset($_SESSION['user_id'])) {
    echo "<h3>Welcome, {$_SESSION['user_name']}</h3>";
} else {
    echo <<<HTML
<h3>Login</h3>
<form action="handle_login.php" method="post">
    <p>
        <label for="user_name">Full Name:</label>
        <input type="text" name="user_name" id="user_name" required>
    </p>
    <p>
        <label for="user_id">ID:</label>
        <input type="text" name="user_id" id="user_id" required>
    </p>
    <input type="submit" value="Login">
</form>
HTML;
}