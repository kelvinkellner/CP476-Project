<?php
include_once(__DIR__.'/../../db/use_db.php');
$is_admin = $_SESSION['user']['is_admin'];
$users = auth_user_get_all();
?>
<table>
    <tr>
        <th>User Name</th>
        <th>User ID</th>
        <th>Is Admin?</th>
        <th>Registration Date</th>
        <th>Actions</th>
    </tr>
    <?php
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>".$user['user_name']."</td>";
            echo "<td>".$user['user_id']."</td>";
            echo "<td>".($user['is_admin']? "YES": "-")."</td>";
            echo "<td>".$user['reg_date']."</td>";
            echo "<td>";
            echo "<form action='auth.php' method='post'>";
            echo "<input type='hidden' name='user_id' value='".$user['user_id']."'>";
            echo "<input type='submit' name='edit' value='Edit'>";
            echo "<input type='submit' name='delete' value='Delete'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
    ?>
</table>