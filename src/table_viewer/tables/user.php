<?php
include_once(__DIR__.'/../../db/use_db.php');
$is_admin = $_SESSION['user']['is_admin'];
$users = (array_key_exists('cache', $_SESSION) and array_key_exists('user', $_SESSION['cache']))? $_SESSION['cache']['user']: auth_user_get_all();
?>
<form id="add">
    <label>Add a new user: </label>
    <input type="text" name="user_name" placeholder="User Name">
    <input type="text" name="user_id" placeholder="User ID">
    <input type="checkbox" name="is_admin" value="1">Is Admin?
</form>
<br/>
<form id="search">
    <label><strong>Search</strong></label><br/>
    <input type="text" name="user_name" placeholder="User Name">
    <input type="text" name="user_id" placeholder="User ID">
    <input type="submit" name="search" value="Search">
    <input type="submit" name="clear" value="Clear Filters">
</form>
<br/>
<table id="user_table">
    <tr>
        <th>User Name</th>
        <th>User ID</th>
        <th>Is Admin?</th>
        <th>Registration Date</th>
        <th>Actions</th>
    </tr>
    <?php
        foreach ($users as $user) {
            echo "<tr class=\"row\">";
            echo "<td>".$user['user_name']."</td>";
            echo "<td>".$user['user_id']."</td>";
            echo "<td>".($user['is_admin']? "YES": "-")."</td>";
            echo "<td>".$user['reg_date']."</td>";
            echo "<td>";
            echo "<form action='auth.php' method='post'>";
            echo "<input type='hidden' name='user_id' value='".$user['user_id']."'>";
            echo "<input type='submit' name='edit' value='Edit'>";
            echo "<button class=\"delete\" onclick=\"(node => node.remove())(this.closest('.row'))\">Delete</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
    ?>
</table>