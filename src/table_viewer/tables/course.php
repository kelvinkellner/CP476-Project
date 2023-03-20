<?php
include_once(__DIR__.'/../../db/use_db.php');
$is_admin = $_SESSION['user']['is_admin'];
$courses = course_get_unique_courses();
?>
<form id="add">
    <label>Add a new course: </label>
    <input type="text" name="course_code" placeholder="Course Code">
    <input type="submit" name="add" value="Add">
</form>
<br/>
<form id="search">
    <label><strong>Search</strong></label><br/>
    <input type="text" name="course_code" placeholder="Course Code">
    <input type="submit" name="search" value="Search">
    <input type="submit" name="clear" value="Clear Filters">
</form>
<br/>
<table id="course_table">
    <tr>
        <th>Course Code</th>
        <th># Students Enrolled</th>
        <?php if ($is_admin) echo "<th>Actions</th>"; ?>
    </tr>
    <?php
        foreach ($courses as $course) {
            echo "<tr class=\"row\">";
            echo "<td>".$course['course_code']."</td>";
            echo "<td>".$course['student_count']."</td>";
            if ($is_admin) {
                echo "<td>";
                echo "<form action='course.php' method='post'>";
                echo "<input type='hidden' name='course_code' value='".$course['course_code']."'>";
                echo "<input type='submit' name='edit' value='Edit'>";
                echo "<button class=\"delete\" onclick=\"(node => node.remove())(this.closest('.row'))\">Delete</button>";
                echo "</form>";
                echo "</td>";
            }
            echo "</tr>";
        }
    ?>
</table>