<?php
include_once(__DIR__.'/../../db/use_db.php');
include_once(__DIR__.'/../search.php');
$is_admin = $_SESSION['user']['is_admin'];
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
if($result)
    $courses = $result;
?>
<form id="add">
    <label>Add a new course: </label>
    <input type="text" name="course_code" placeholder="Course Code">
    <input type="submit" name="add" value="Add">
</form>
<br/>
<?php $search->show(); ?>
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