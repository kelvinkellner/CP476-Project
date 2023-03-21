<?php
include_once(__DIR__.'/../../db/use_db.php');
include_once(__DIR__.'/../search.php');
include_once(__DIR__.'/../changes.php');
$is_admin = $_SESSION['user']['is_admin'];
$users = (array_key_exists('cache', $_SESSION) and array_key_exists('user', $_SESSION['cache']))? $_SESSION['cache']['user']: auth_user_get_all();
$search = new SearchBar(
    ['user_name' => 'User Name', 'user_id' => 'User ID'],
    'user',
    'auth_user_search'
);
$result = $search->check_for_searches();
if($result)
    $users = $result;
$changes = new ChangeManager(
    'user',
    [
        'add' =>
        [
            'fields' => 
                [
                    ['name' => 'user_name', 'type' => 'text', 'label' => 'User Name'],
                    ['name' => 'user_id', 'type' => 'text', 'label' => 'User ID'],
                    ['name' => 'is_admin', 'type' => 'checkbox', 'label' => 'Is Admin?']
                ],
            'label' => 'Add a new user: ',
            'submit_function' => 'auth_user_add',
            'on_success_function' => function () {
                global $search;
                global $users;
                $search->clear_text_fields();
                $_SESSION['cache']['user'] = auth_user_get_all();
                $users = $_SESSION['cache']['user'];
                return $users;
            }
        ]
    ]
);
$result = $changes->check_for_changes();
if ($result)
    $users = $result;
?>
<?php $changes->show_add(); ?>
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