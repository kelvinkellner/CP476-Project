<?php
include_once(__DIR__.'/../../db/use_db.php');
$is_admin = $_SESSION['user']['is_admin'];
$courses = course_get_unique_courses();
?>
<table>
    <tr>
        <th>Course Code</th>
        <th># Students Enrolled</th>
        <?php if ($is_admin) echo "<th>Actions</th>"; ?>
    </tr>
    <?php
        foreach ($courses as $course) {
            echo "<tr>";
            echo "<td>".$course['course_code']."</td>";
            echo "<td>".$course['student_count']."</td>";
            if ($is_admin) {
                echo "<td>";
                echo "<form action='course.php' method='post'>";
                echo "<input type='hidden' name='course_code' value='".$course['course_code']."'>";
                echo "<input type='submit' name='edit' value='Edit'>";
                echo "<input type='submit' name='delete' value='Delete'>";
                echo "</form>";
                echo "</td>";
            }
            echo "</tr>";
        }
    ?>
</table>