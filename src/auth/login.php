<?php
require_once(__DIR__.'/../db/use_db.php');

if (isset($_SESSION['user'])) {
    echo "<h3>Welcome, {$_SESSION['user']['user_name']}</h3>";
} else {
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    echo <<<HTML
<h3>Login</h3>
<form action="/CP476/src/auth/handle_login.php" method="post">
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