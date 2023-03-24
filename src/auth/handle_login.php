<?php
require_once(__DIR__.'/../db/use_db.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form
    $user_name = $_POST['user_name'];
    $user_id = $_POST['user_id'];
    $user = auth_login($user_name, $user_id);
    if ($user) { // Success!
        header("Location: {$_SERVER["HTTP_REFERER"]}");
    } else { // Problem!
        echo '<p>Redirecting...</p>';
        echo "<script>
        alert('Login failed. Please try again.');
        window.location.href='/CP476/index.php';
        </script>";
    }
} 
?>