<?php
include_once(__DIR__.'/selector.php');
echo '<br/>';
$is_admin = $_SESSION['user']['is_admin'];
# Use context to display appropriate table
if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(isset($_POST['student']))
        include_once(__DIR__.'/tables/student.php');
    if(isset($_POST['course']))
        include_once(__DIR__.'/tables/course.php');
    if(isset($_POST['grade']))
        include_once(__DIR__.'/tables/grade.php');
    if(isset($_POST['user']) and $is_admin)
        include_once(__DIR__.'/tables/user.php');
}
?>