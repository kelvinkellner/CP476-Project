<?php
// Define Root Constants, etc.
require_once(__DIR__.'/private.php');
define('SITE_ROOT', __DIR__.'/../');
session_start();
if (!is_writable(session_save_path())) {
    echo 'Session path "'.session_save_path().'" is not writable for PHP!'; 
}
?>