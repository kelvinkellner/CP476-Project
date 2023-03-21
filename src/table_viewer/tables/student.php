<?php
include_once(__DIR__.'/../../db/use_db.php');
include_once(__DIR__.'/../search.php');
$is_admin = $_SESSION['user']['is_admin'];
$students = (array_key_exists('cache', $_SESSION) and array_key_exists('student', $_SESSION['cache']))? $_SESSION['cache']['student']: student_get_all();
$search = new SearchBar(
    ['student_id' => 'Student ID', 'student_name' => 'Student Name'],
    'student',
    'student_search'
);
$result = $search->check_for_searches();
if($result)
    $students = $result;
if($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle actions
    if(isset($_POST['add'])) {
        if(($_POST['student_id'] !== '') and ($_POST['student_name'] !== '')) {
            if (!auth_user_add($_POST['student_id'], $_POST['student_name']))
                echo "<script>alert('Student with that ID already exists!')</script>";
            else {
                $search->clear_text_fields();
                $_SESSION['cache']['user'] = auth_user_get_all();
                $users = $_SESSION['cache']['user'];
            }
        }
        else
            echo "<script>alert('All fields are required!')</script>";
    }
}
?>
<form id="add">
    <label>Add a new student: </label>
    <input type="text" name="student_id" placeholder="Student ID">
    <input type="text" name="student_name" placeholder="Student Name">
    <input type="submit" name="add" value="Add">
</form>
<br/>
<?php $search->show(); ?>
<table id="students_table">
    <tr>
        <th>Student ID</th>
        <th>Student Name</th>
        <?php if ($is_admin) echo "<th>Actions</th>"; ?>
    </tr>
    <?php
        foreach ($students as $student) {
            echo "<tr class=\"row\">";
            if ($is_admin) {
                echo "<td id=\"student_id\" contentEditable=\"true\">".$student['student_id']."</td>";
                echo "<td id=\"student_name\" contentEditable=\"true\">".$student['student_name']."</td>";
                echo "<td>";
                echo "<input type='hidden' name='student_id' value='".$student['student_id']."'>";
                echo "<input type='submit' name='edit' value='Edit'>";
                echo "<button class=\"delete\" onclick=\"(node => node.remove())(this.closest('.row'))\">Delete</button>";
                echo "</td>";
            } else {
                echo "<td>".$student['student_id']."</td>";
                echo "<td>".$student['student_name']."</td>";
            }
            echo "</tr>";
        }
    ?>
</table>
<form action='/CP476/src/table_viewer/handle_changes.php' method='post'>
</form>