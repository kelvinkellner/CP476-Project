<?php
include_once(__DIR__.'/selector.php');
echo '<br/>';
# Use context to display appropriate table
if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(isset($_POST['name']))
        include_once(__DIR__.'/tables/name.php');
    if(isset($_POST['course']))
        include_once(__DIR__.'/tables/course.php');
    if(isset($_POST['grade']))
        include_once(__DIR__.'/tables/grade.php');
}
?>