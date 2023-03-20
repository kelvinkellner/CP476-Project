<?php
include_once(__DIR__.'/../db/use_db.php');
$is_admin = $_SESSION['user']['is_admin'];
?>
<form  method="post">
    <input type="submit" name="student" value="Students">
    <input type="submit" name="course" value="Courses">
    <input type="submit" name="grade" value="Grades" >
    <?php if ($is_admin) echo "<input type='submit' name='user' value='Users'>"; ?>
</form>