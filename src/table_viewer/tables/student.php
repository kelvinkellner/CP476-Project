<?php
include_once(__DIR__.'/../../db/use_db.php');
$is_admin = $_SESSION['user']['is_admin'];
$students = student_get_all();
?>
<table>
    <tr>
        <th>Student ID</th>
        <th>Student Name</th>
        <?php if ($is_admin) echo "<th>Actions</th>"; ?>
    </tr>
    <?php
        foreach ($students as $student) {
            echo "<tr>";
            echo "<td>".$student['student_id']."</td>";
            echo "<td>".$student['student_name']."</td>";
            if ($is_admin) {
                echo "<td>";
                echo "<form action='student.php' method='post'>";
                echo "<input type='hidden' name='student_id' value='".$student['student_id']."'>";
                echo "<input type='submit' name='edit' value='Edit'>";
                echo "<input type='submit' name='delete' value='Delete'>";
                echo "</form>";
                echo "</td>";
            }
            echo "</tr>";
        }
    ?>
</table>