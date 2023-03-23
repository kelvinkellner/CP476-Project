<?php
include_once(__DIR__.'/../../db/use_db.php');
include_once(__DIR__.'/../search.php');
include_once(__DIR__.'/../changes.php');
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
if($is_admin) {
    $changes = new ChangeManager(
        'grade',
        [
            'add' =>
            [
                'fields' => 
                    [
                        ['name' => 'student_id', 'type' => 'text', 'label' => 'Student ID'],
                        ['name' => 'student_name', 'type' => 'text', 'label' => 'Student Name'],
                        ['name' => 'course_code', 'type' => 'text', 'label' => 'Course Code'],
                        ['name' => 'grade_test_1', 'type' => 'text', 'label' => 'Test 1'],
                        ['name' => 'grade_test_2', 'type' => 'text', 'label' => 'Test 2'],
                        ['name' => 'grade_test_3', 'type' => 'text', 'label' => 'Test 3'],
                        ['name' => 'grade_exam', 'type' => 'text', 'label' => 'Final Exam'],
                    ],
                'label' => 'Add a new grade entry: ',
                'submit_function' => 'grade_add_entry',
                'on_success_function' => function () {
                    global $search;
                    global $courses;
                    global $student_grades;
                    global $course_grade_lookup;
                    $search->clear_text_fields();
                    $_SESSION['cache']['grade_course'] = course_get_all();
                    $courses = $_SESSION['cache']['grade_course'];
                    $_SESSION['cache']['grade'] = grade_get_all();
                    $student_grades = $_SESSION['cache']['grade'];
                    $course_grade_lookup = [];
                    foreach ($courses as $row)
                        $course_grade_lookup[$row['student_id']][$row['course_code']] = $row;
                    return $student_grades;
                }
            ],
            'edit' =>
            [
                'fields' => 
                [
                    ['name' => 'og_student_id'],
                    ['name' => 'og_course_code'],
                    ['name' => 'student_id'],
                    ['name' => 'student_name'],
                    ['name' => 'course_code'],
                    ['name' => 'grade_test_1'],
                    ['name' => 'grade_test_2'],
                    ['name' => 'grade_test_3'],
                    ['name' => 'grade_exam']
                ],
                'submit_function' => 'grade_update_entry',
                'on_success_function' => function () {
                    global $courses;
                    global $student_grades;
                    global $course_grade_lookup;
                    $_SESSION['cache']['grade_course'] = course_get_all();
                    $courses = $_SESSION['cache']['grade_course'];
                    $_SESSION['cache']['grade'] = grade_get_all();
                    $student_grades = $_SESSION['cache']['grade'];
                    $course_grade_lookup = [];
                    foreach ($courses as $row)
                        $course_grade_lookup[$row['student_id']][$row['course_code']] = $row;
                    echo "<p class='success_message'>Entry updated successfully!</p><br/>";
                    return $student_grades;
                }
            ],
            'delete' =>
            [
                'fields' => 
                [
                    'student_id',
                    'course_code'
                ],
                'submit_function' => 'grade_delete_entry',
                'on_success_function' => function () {
                    global $courses;
                    global $student_grades;
                    global $course_grade_lookup;
                    $_SESSION['cache']['grade_course'] = course_get_all();
                    $courses = $_SESSION['cache']['grade_course'];
                    $_SESSION['cache']['grade'] = grade_get_all();
                    $student_grades = $_SESSION['cache']['grade'];
                    $course_grade_lookup = [];
                    foreach ($courses as $row)
                        $course_grade_lookup[$row['student_id']][$row['course_code']] = $row;
                    return $student_grades;
                }
            ]
        ]
    );
    $result = $changes->check_for_changes();
    if ($result)
        $students = $result;
}
?>
<?php if ($is_admin) $changes->show_add(); ?>
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
            echo "<form id='changes' method='post'>";
            echo "<input type='hidden' name='grade' value='true'>";
            if ($is_admin) {
                echo "<input type='hidden' name='og_student_id' value='".$row['student_id']."'>";
                echo "<input type='hidden' name='og_course_code' value='".$row['course_code']."'>";
                echo "<input type='hidden' name='student_id' value='".$row['student_id']."'>";
                echo "<input type='hidden' name='student_name' value='".$row['student_name']."'>";
                echo "<input type='hidden' name='course_code' value='".$row['course_code']."'>";
                echo "<td>".$row['student_id']."</td>";
                echo "<td>".$row['student_name']."</td>";
                echo "<td><input name='course_code' size='8' type='text' class='show-input-as-plain-text' value='".$row['course_code']."'></td>";
                echo "<td><input name='grade_test_1' size='8' type='text' class='show-input-as-plain-text' value='".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_1']."'></td>";
                echo "<td><input name='grade_test_2' size='8' type='text' class='show-input-as-plain-text' value='".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_2']."'></td>";
                echo "<td><input name='grade_test_3' size='8' type='text' class='show-input-as-plain-text' value='".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_3']."'></td>";
                echo "<td><input name='grade_exam' size='8' type='text' class='show-input-as-plain-text' value='".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_exam']."'></td>";
                echo "<td>".$row['grade_final']."</td>";
                echo "<td>";
                $changes->show_edit();
                $changes->show_delete();
                echo "</td>";
            } else {
                echo "<td>".$row['student_id']."</td>";
                echo "<td>".$row['student_name']."</td>";
                echo "<td>".$row['course_code']."</td>";
                echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_1']."</td>";
                echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_2']."</td>";
                echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_test_3']."</td>";
                echo "<td>".$course_grade_lookup[$row['student_id']][$row['course_code']]['grade_exam']."</td>";
                echo "<td>".$row['grade_final']."</td>";
            }
            echo "</form>";
            echo "</tr>";
        }
    ?>
</table>