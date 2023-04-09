<?php
include_once(__DIR__.'/../../db/use_db.php');
include_once(__DIR__.'/../changes.php');
$courses = course_get_all();
$changes = new ChangeManager(
    'course',
    [
        'edit' =>
        [
            'fields' => 
            [
                ['name' => 'og_student_id'],
                ['name' => 'og_course_code'],
                ['name' => 'student_id'],
                ['name' => 'course_code'],
                ['name' => 'grade_test_1'],
                ['name' => 'grade_test_2'],
                ['name' => 'grade_test_3'],
                ['name' => 'grade_exam']
            ],
            'submit_function' => 'course_update',
            'on_success_function' => function () {
                echo "<p class='success_message'>Course entry updated successfully!</p>";
                return course_get_all();
            }
        ]
    ]
);
$result = $changes->check_for_changes();
if ($result)
    $courses = $result;
?>
<br/>
<table id="course_table">
    <tr>
        <th>Student ID</th>
        <th>Course Code</th>
        <th>Test 1</th>
        <th>Test 2</th>
        <th>Test 3</th>
        <th>Final Exam</th>
        <th>Actions</th>
    </tr>
    <?php
        foreach ($courses as $row) {
            echo "<tr class=\"row\">";
            echo "<form id='changes' method='post'>";
            echo "<input type='hidden' name='course' value='true'>";
            echo "<input type='hidden' name='og_student_id' value='".$row['student_id']."'>";
            echo "<input type='hidden' name='og_course_code' value='".$row['course_code']."'>";
            echo "<input type='hidden' name='student_id' value='".$row['student_id']."'>";
            echo "<input type='hidden' name='course_code' value='".$row['course_code']."'>";
            echo "<td>".$row['student_id']."</td>";
            echo "<td><input name='course_code' size='8' type='text' class='show-input-as-plain-text' value='".$row['course_code']."'></td>";
            echo "<td><input name='grade_test_1' size='8' type='text' class='show-input-as-plain-text' value='".$row['grade_test_1']."'></td>";
            echo "<td><input name='grade_test_2' size='8' type='text' class='show-input-as-plain-text' value='".$row['grade_test_2']."'></td>";
            echo "<td><input name='grade_test_3' size='8' type='text' class='show-input-as-plain-text' value='".$row['grade_test_3']."'></td>";
            echo "<td><input name='grade_exam' size='8' type='text' class='show-input-as-plain-text' value='".$row['grade_exam']."'></td>";
            echo "<td>";
            $changes->show_edit();
            echo "</td>";
            echo "</form>";
            echo "</tr>";
        }
    ?>
</table>