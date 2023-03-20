<?php
include_once(__DIR__.'/../../db/use_db.php');
$is_admin = $_SESSION['user']['is_admin'];
$courses = course_get_all();
$student_grades = grade_get_all();
$student_grade_lookup = [];
foreach ($student_grades as $student_grade) 
    $student_grade_lookup[$student_grade['student_id']][$student_grade['course_code']] = $student_grade;
?>

<table>
    <tr>
        <th>Student ID</th>
        <th>Student Name</th>
        <th>Course Code</th>
        <th>Test 1</th>
        <th>Test 2</th>
        <th>Test 3</th>
        <th>Final Exam</th>
        <th>Final Grade</th>
        <?php if ($is_admin) echo "<th>Actions</th>"; ?>
    </tr>
    <?php
        foreach ($courses as $row) {
            echo "<tr>";
            echo "<td>".$row['student_id']."</td>";
            echo "<td>".$student_grade_lookup[$row['student_id']][$row['course_code']]['student_name']."</td>";
            echo "<td>".$row['course_code']."</td>";
            echo "<td>".$row['grade_test_1']."</td>";
            echo "<td>".$row['grade_test_2']."</td>";
            echo "<td>".$row['grade_test_3']."</td>";
            echo "<td>".$row['grade_exam']."</td>";
            echo "<td>".$student_grade_lookup[$row['student_id']][$row['course_code']]['grade_final']."</td>";
            if ($is_admin) {
                echo "<td>";
                echo "<form action='student.php' method='post'>";
                echo "<input type='hidden' name='student_id' value='".$row['student_id']."'>";
                echo "<input type='hidden' name='course_code' value='".$row['course_code']."'>";
                echo "<input type='submit' name='edit' value='Edit'>";
                echo "<input type='submit' name='delete' value='Delete'>";
                echo "</form>";
                echo "</td>";
            }
            echo "</tr>";
        }
    ?>
</table>