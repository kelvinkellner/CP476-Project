<?php
include_once(__DIR__.'/../../db/use_db.php');
$is_admin = $_SESSION['user']['is_admin'];
$courses = (array_key_exists('cache', $_SESSION) and array_key_exists('course', $_SESSION['cache']))? $_SESSION['cache']['course']: course_get_all();
$student_grades = (array_key_exists('cache', $_SESSION) and array_key_exists('grade', $_SESSION['cache']))? $_SESSION['cache']['grade']: grade_get_all();
$student_grade_lookup = [];
foreach ($student_grades as $student_grade) 
    $student_grade_lookup[$student_grade['student_id']][$student_grade['course_code']] = $student_grade;
?>
<form id="add">
    <label>Add a new grade entry: </label>
    <input type="text" name="student_id" placeholder="Student ID">
    <input type="text" name="student_name" placeholder="Student Name">
    <input type="text" name="course_code" placeholder="Course Code">
    <input type="text" name="grade_test_1" placeholder="Test 1">
    <input type="text" name="grade_test_2" placeholder="Test 2">
    <input type="text" name="grade_test_3" placeholder="Test 3">
    <input type="text" name="grade_exam" placeholder="Final Exam">
    <input type="submit" name="add" value="Add">
</form>
<br/>
<form id="search">
    <label><strong>Search</strong></label><br/>
    <input type="text" name="student_id" placeholder="Student ID">
    <input type="text" name="student_name" placeholder="Student Name">
    <input type="text" name="course_code" placeholder="Course Code">
    <input type="submit" name="search" value="Search">
    <input type="submit" name="clear" value="Clear Filters">
</form>
<br/>
<table id="grade_table">
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
            echo "<tr class=\"row\">";
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
                echo "<button class=\"delete\" onclick=\"(node => node.remove())(this.closest('.row'))\">Delete</button>";
                echo "</form>";
                echo "</td>";
            }
            echo "</tr>";
        }
    ?>
</table>