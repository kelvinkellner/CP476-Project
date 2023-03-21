<?php
include_once(__DIR__.'/../../db/use_db.php');
include_once(__DIR__.'/../search.php');
$is_admin = $_SESSION['user']['is_admin'];
$users = (array_key_exists('cache', $_SESSION) and array_key_exists('user', $_SESSION['cache']))? $_SESSION['cache']['user']: auth_user_get_all();
$search = new SearchBar(
    ['user_name' => 'User Name', 'user_id' => 'User ID'],
    'user',
    'auth_user_search'
);
$result = $search->check_for_searches();
if($result) {
    $users = $result;
    $_SESSION['cache']['user'] = $users;
}

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
<?php $search->show(); ?>
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