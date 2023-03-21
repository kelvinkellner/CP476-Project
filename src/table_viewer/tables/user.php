<?php
include_once(__DIR__.'/../../db/use_db.php');
$is_admin = $_SESSION['user']['is_admin'];
$users = (array_key_exists('cache', $_SESSION) and array_key_exists('user', $_SESSION['cache']))? $_SESSION['cache']['user']: auth_user_get_all();
$search_user_name = array_key_exists('search_user_name', $_SESSION)? $_SESSION['search_user_name']: '';
$search_user_id = array_key_exists('search_user_id', $_SESSION)? $_SESSION['search_user_id']: '';

if($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle actions
    if(isset($_POST['add'])) {
        if(($_POST['user_name'] !== '') and ($_POST['user_id'] !== '')) {
            if (!auth_user_add($_POST['user_name'], $_POST['user_id'], (isset($_POST['user_is_admin']) and ($_POST['user_is_admin'] == '1'))? 1 : 0))
                echo "<script>alert('User with that ID already exists!')</script>";
            else {
                unset($_SESSION['search_user_name']);
                unset($_SESSION['search_user_id']);
                $_SESSION['cache']['user'] = auth_user_get_all();
                $users = $_SESSION['cache']['user'];
            }
        }
        else
            echo "<script>alert('All fields are required!')</script>";
    }
    if(isset($_POST['search'])) {
        $_SESSION['search_user_name'] = $_POST['user_name'];
        $_SESSION['search_user_id'] = $_POST['user_id'];
        $users = auth_user_search($_POST['user_name'], $_POST['user_id']);
    }
    if(isset($_POST['clear'])) {
        unset($_SESSION['search_user_name']);
        unset($_SESSION['search_user_id']);
        $_SESSION['cache']['user'] = auth_user_get_all();
    }
}
?>
<form id="add" method="post">
    <input type="hidden" name="user" value="true">
    <label>Add a new user: </label>
    <input type="text" name="user_name" placeholder="User Name">
    <input type="text" name="user_id" placeholder="User ID">
    Is Admin?<input type="checkbox" name="user_is_admin" value="1">
    <input type="submit" name="add" value="Add">
</form>
<br/>
<form id="search" method="post">
    <input type="hidden" name="user" value="true">
    <label><strong>Search</strong></label><br/>
    <input type="text" name="user_name" placeholder="User Name" value=<?php echo array_key_exists('search_user_name', $_SESSION)? $_SESSION['search_user_name']: '' ?>>
    <input type="text" name="user_id" placeholder="User ID" value=<?php echo array_key_exists('search_user_id', $_SESSION)? $_SESSION['search_user_id']: '' ?>>
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
            echo "<td>".($user['is_admin']? "YES": "NO")."</td>";
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