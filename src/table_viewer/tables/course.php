<?php
include_once(__DIR__.'/../../db/use_db.php');
include_once(__DIR__.'/../search.php');
if(array_key_exists('cache', $_SESSION) and array_key_exists('course', $_SESSION['cache']))
    $courses = $_SESSION['cache']['course'];
else {
    $courses = course_get_unique_courses();
    $_SESSION['cache']['course'] = $courses;
}
$search = new SearchBar(
    ['course_code' => 'Course Code'],
    'course',
    'course_search_unique_courses'
);
$result = $search->check_for_searches();
if($result !== null) {
    $courses = $result;
    $_SESSION['cache']['course'] = $courses;
}
?>
<?php $search->show(); ?>
<br/>
<table id="course_table">
    <tr>
        <th>Course Code</th>
        <th># Students Enrolled</th>
    </tr>
    <?php
        foreach ($courses as $course) {
            echo "<tr class=\"row\">";
            echo "<td>".$course['course_code']."</td>";
            echo "<td>".$course['student_count']."</td>";
            echo "</tr>";
        }
    ?>
</table>
<p><strong>Note:</strong> Courses are not directly editable. To modify the content of this page, make changes to Grades and they will be automatically reflected here.</p>