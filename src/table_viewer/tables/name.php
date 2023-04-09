<?php
include_once(__DIR__.'/../../db/use_db.php');
include_once(__DIR__.'/../changes.php');
$names = name_get_all();
$changes = new ChangeManager(
    'name',
    [
        'edit' =>
        [
            'fields' => 
            [
                ['name' => 'og_student_id'],
                ['name' => 'student_id'],
                ['name' => 'student_name']
            ],
            'submit_function' => 'name_update',
            'on_success_function' => function () {
                echo "<p class='success_message'>Student updated successfully!</p>";
                return name_get_all();
            }
        ]
    ]
);
$result = $changes->check_for_changes();
if ($result)
    $names = $result;
?>
<br/>
<table id="name_table">
    <tr>
        <th>Student ID</th>
        <th>Student Name</th>
        <th>Actions</th>
    </tr>
    <?php
        foreach ($names as $name) {
            echo "<tr class=\"row\">";
            echo "<form id='changes' method='post'>";
            echo "<input type='hidden' name='name' value='true'>";
            echo "<input type='hidden' name='og_student_id' value='".$name['student_id']."'>";
            echo "<td><input name='student_id' size='10' type='text' class='show-input-as-plain-text' value='".$name['student_id']."'></td>";
            echo "<td><input name='student_name' size='20' type='text' class='show-input-as-plain-text' value='".$name['student_name']."'></td>";
            echo "<td>";
            $changes->show_edit();
            echo "</td>";
            echo "</form>";
            echo "</tr>";
        }
    ?>
</table>
<form action='/CP476/src/table_viewer/handle_changes.php' method='post'>
</form>