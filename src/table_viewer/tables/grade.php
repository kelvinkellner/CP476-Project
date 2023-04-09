<?php
include_once(__DIR__.'/../../db/use_db.php');
grade_refresh_final_grades();
$grades = grade_get_all();
?>
<br/>
<table id="grade_table">
    <tr>
        <th>Student ID</th>
        <th>Student Name</th>
        <th>Course Code</th>
        <th>Final Grade</th>
    </tr>
    <?php
        foreach ($grades as $row) {
            echo "<tr class=\"row\">";
            echo "<td>".$row['student_id']."</td>";
            echo "<td>".$row['student_name']."</td>";
            echo "<td>".$row['course_code']."</td>";
            echo "<td>".$row['grade_final']."</td>";
            echo "</tr>";
        }
    ?>
</table>