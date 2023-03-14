<?php
// Strict Typing Mode
declare(strict_types = 1);
// Import Root Constants, etc.
include_once(__DIR__.'/../config/config.php');
?>

<?php
include_once(__DIR__.'/inc/head.php'); 
include_once(SITE_ROOT.'/config/private.php');
?>

<h1>My Classroom Grading Application</h1> 

<?php include_once(__DIR__.'/inc/menu.php'); ?> 

<h3>Section Title</h3> 

<?php 
// Your main content goes here 
?> 

<?php include_once(SITE_ROOT.'/modules/init_db/init_db.php'); ?>
<?php init_db(); ?>

<?php include_once(__DIR__.'/inc/footer.php'); ?>