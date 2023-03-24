<?php
include_once(__DIR__.'/../../db/use_db.php');
include_once(__DIR__.'/../search.php');
include_once(__DIR__.'/../changes.php');
$is_admin = $_SESSION['user']['is_admin'];
$students = (array_key_exists('cache', $_SESSION) and array_key_exists('student', $_SESSION['cache']))? $_SESSION['cache']['student']: student_get_all();
$search = new SearchBar(
    ['student_id' => 'Student ID', 'student_name' => 'Student Name'],
    'student',
    'student_search'
);
$result = $search->check_for_searches();
if($result !== null)
    $students = $result;
if($is_admin) {
    $changes = new ChangeManager(
        'student',
        [
            'add' =>
            [
                'fields' => 
                    [
                        ['name' => 'student_id', 'type' => 'text', 'label' => 'Student ID'],
                        ['name' => 'student_name', 'type' => 'text', 'label' => 'Student Name'],
                    ],
                'label' => 'Add a new student: ',
                'submit_function' => 'student_add',
                'on_success_function' => function () {
                    global $search;
                    global $students;
                    $search->clear_text_fields();
                    $_SESSION['cache']['student'] = student_get_all();
                    $students = $_SESSION['cache']['student'];
                    return $students;
                }
            ],
            'edit' =>
            [
                'fields' => 
                [
                    ['name' => 'og_student_id'],
                    ['name' => 'student_id'],
                    ['name' => 'student_name']
                ],
                'submit_function' => 'student_update',
                'on_success_function' => function () {
                    $_SESSION['cache']['student'] = student_get_all();
                    echo "<p class='success_message'>Student updated successfully!</p><br/>";
                    return $_SESSION['cache']['student'];
                }
            ],
            'delete' =>
            [
                'fields' => 
                [
                    'student_id'
                ],
                'submit_function' => 'student_delete',
                'on_success_function' => function () {
                    $_SESSION['cache']['student'] = student_get_all();
                    return $_SESSION['cache']['student'];
                }
            ]
        ]
    );
    $result = $changes->check_for_changes();
    if ($result)
        $students = $result;
}
?>
<?php
if($is_admin) {
    $changes->show_add();
    echo "<br/>";
}
?>
<?php $search->show(); ?>
<table id="student_table">
    <tr>
        <th>Student ID</th>
        <th>Student Name</th>
        <?php if ($is_admin) echo "<th>Actions</th>"; ?>
    </tr>
    <?php
        foreach ($students as $student) {
            echo "<tr class=\"row\">";
            echo "<form id='changes' method='post'>";
            echo "<input type='hidden' name='student' value='true'>";
            if ($is_admin) {
                echo "<input type='hidden' name='og_student_id' value='".$student['student_id']."'>";
                echo "<td><input name='student_id' size='10' type='text' class='show-input-as-plain-text' value='".$student['student_id']."'></td>";
                echo "<td><input name='student_name' size='20' type='text' class='show-input-as-plain-text' value='".$student['student_name']."'></td>";
                echo "<td>";
                $changes->show_edit();
                $changes->show_delete();
                echo "</td>";
            } else {
                echo "<td>".$student['student_id']."</td>";
                echo "<td>".$student['student_name']."</td>";
            }
            echo "</form>";
            echo "</tr>";
        }
    ?>
</table>
<form action='/CP476/src/table_viewer/handle_changes.php' method='post'>
</form>