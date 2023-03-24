<?php
include_once(__DIR__.'/../../db/use_db.php');
include_once(__DIR__.'/../search.php');
if(array_key_exists('cache', $_SESSION) and array_key_exists('student_view_courses', $_SESSION['cache']))
    $student_view_courses = $_SESSION['cache']['student_view_courses'];
else {
    $student_view_courses = course_get_courses_by_student_id($_SESSION['user']['user_id']);
    $_SESSION['cache']['student_view_courses'] = $student_view_courses;
}
if(array_key_exists('cache', $_SESSION) and array_key_exists('student_view_grades', $_SESSION['cache']))
    $student_view_grades = $_SESSION['cache']['student_view_grades'];
else {
    $student_view_grades = grade_get_grades_by_student_id($_SESSION['user']['user_id']);
    $_SESSION['cache']['student_view_grades'] = $student_view_grades;
}
$search = new SearchBar(
    ['course_code' => 'Course Code'],
    'student_view',
    function ($course_code) {
        return grade_get_by_student_id_search_by_course_code($_SESSION['user']['user_id'], $course_code);
    }
);
$result = $search->check_for_searches();
if($result)
    $student_view_grades = $result;
$course_grade_lookup = [];
foreach ($student_view_courses as $row)
    $course_grade_lookup[$row['student_id']][$row['course_code']] = $row;
?>
<br/>
<?php $search->show(); ?>
<br/>
<table id="student_view_table">
    <tr>
        <th>Course Code</th>
        <th>Test 1</th>
        <th>Test 2</th>
        <th>Test 3</th>
        <th>Final Exam</th>
        <th>Final Grade</th>
    </tr>
    <?php
        foreach ($student_view_grades as $row) {
            echo "<tr class=\"row\">";
            echo "<td>".$row['course_code']."</td>";
            echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_1']."</td>";
            echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_2']."</td>";
            echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_3']."</td>";
            echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_exam']."</td>";
            echo "<td>".$row['grade_final']."</td>";
            echo "</tr>";
        }
    ?>
</table>
<p>Grade table is for student: <?php echo $_SESSION['user']['user_name'] . ' (' . $_SESSION['user']['user_id'] . ')'; ?></p>