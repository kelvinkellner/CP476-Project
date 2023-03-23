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
        ],
        'edit' =>
        [
            'fields' => 
            [
                ['name' => 'og_user_name'],
                ['name' => 'og_user_id'],
                ['name' => 'user_name'],
                ['name' => 'user_id'],
                ['name' => 'is_admin', 'type' => 'yes-no']
            ],
            'submit_function' => 'auth_user_update',
            'on_success_function' => function () {
                $_SESSION['cache']['user'] = auth_user_get_all();
                echo "<p class='success_message'>User updated successfully!</p><br/>";
                return $_SESSION['cache']['user'];
            }
        ],
        'delete' =>
        [
            'fields' =>
            [
                'user_name',
                'user_id'
            ],
            'submit_function' => 'auth_user_delete',
            'on_success_function' => function () {
                $_SESSION['cache']['user'] = auth_user_get_all();
                return $_SESSION['cache']['user'];
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
        <th>Last Modified</th>
        <th>Actions</th>
    </tr>
    <?php
        foreach ($users as $user) {
            echo "<tr class=\"row\">";
            echo "<form id='changes' method='post'>";
            echo "<input type='hidden' name='user' value='true'>";
            echo "<input type='hidden' name='og_user_name' value='".$user['user_name']."'>";
            echo "<input type='hidden' name='og_user_id' value='".$user['user_id']."'>";
            echo "<td><input name='user_name' size='20' type='text' class='show-input-as-plain-text' value='".$user['user_name']."'></td>";
            echo "<td><input name='user_id' size='8' type='text' class='show-input-as-plain-text' value='".$user['user_id']."'></td>";
            echo "<td><input name='is_admin' size='8' type='text' class='show-input-as-plain-text' value='".($user['is_admin']? "YES": "NO")."'></td>";
            echo "<td>".$user['mod_date']."</td>";
            echo "<td>";
            $changes->show_edit();
            $changes->show_delete();
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
    ?>
</table>