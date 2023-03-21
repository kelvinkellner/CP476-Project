<?php
include_once(__DIR__.'/../../db/use_db.php');
include_once(__DIR__.'/../search.php');
$is_admin = $_SESSION['user']['is_admin'];
if(array_key_exists('cache', $_SESSION) and array_key_exists('grade_course', $_SESSION['cache']))
    $courses = $_SESSION['cache']['grade_course'];
else {
    $courses = course_get_all();
    $_SESSION['cache']['grade_course'] = $courses;
}
if(array_key_exists('cache', $_SESSION) and array_key_exists('grade', $_SESSION['cache']))
    $student_grades = $_SESSION['cache']['grade'];
else {
    $student_grades = grade_get_all();
    $_SESSION['cache']['grade'] = $student_grades;
}
$search = new SearchBar(
    ['student_id' => 'Student ID', 'student_name' => 'Student Name', 'course_code' => 'Course Code'],
    'grade',
    'grade_search'
);
$result = $search->check_for_searches();
if($result)
    $student_grades = $result;
$course_grade_lookup = [];
foreach ($courses as $row)
    $course_grade_lookup[$row['student_id']][$row['course_code']] = $row;
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
<?php $search->show(); ?>
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
        foreach ($student_grades as $row) {
            echo "<tr class=\"row\">";
            echo "<td>".$row['student_id']."</td>";
            echo "<td>".$row['student_name']."</td>";
            echo "<td>".$row['course_code']."</td>";
            echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_1']."</td>";
            echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_2']."</td>";
            echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_3']."</td>";
            echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_exam']."</td>";
            echo "<td>".$row['grade_final']."</td>";
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