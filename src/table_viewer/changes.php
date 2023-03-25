<?php
class ChangeManager {
    private $context_name;
    private $actions;

    public function __construct($context_name, $actions) {
        $this->context_name = $context_name;
        $this->actions = $actions;
    }

    public function show_add() {
        $add = $this->actions['add'];
        $fields = $add['fields'];
        $label = $add['label'];
        echo "<form id='add' method='post'>";
        echo "<input type='hidden' name='$this->context_name' value='true'>";
        echo "<label>".$label."</label>";
        foreach ($fields as $data) {
            $name = $data['name'];
            $label = $data['label'];
            $type = $data['type'];
            if ($type === 'text')
                echo "<input type='$type' name='$name' placeholder='$label'>";
            if ($type === 'checkbox')
                echo "$label<input type='$type' name='$name' value='1'>";
        }
        echo "<input type='submit' name='add' value='Add'>";
        echo "</form>";
    }

    public function show_edit() {
        echo "<input type='submit' name='edit' value='Save Changes'>";
    }

    public function show_delete() {
        echo "<input type='submit' name='delete' value='Delete'>";
    }

    public function check_for_changes() {
        $result = null;
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['add'])) { // Handle Adds
                $add = $this->actions['add'];
                $fields = $add['fields'];
                $submit = $add['submit_function'];
                $on_success = $add['on_success_function'];
                $args = [];
                foreach ($fields as $data) {
                    $field = $data['name'];
                    if ($data['type'] === 'checkbox')
                        $args[$field] = (isset($_POST[$field]) && $_POST[$field]) == '1'? 1: 0;
                    else {
                        if ($_POST[$field] === '') {
                            echo "<script>alert('All fields are required!')</script>";
                            return false;
                        }
                        $args[$field] = $_POST[$field];
                    }
                }
                $result = call_user_func_array($submit, $args);
                if (!$result) {
                    echo "<script>alert('Failed to add new $this->context_name. Check that ID(s) are not already in use.')</script>";
                    return false;
                }
                else
                    $result = call_user_func($on_success);
            }
            if(isset($_POST['edit'])) { // Handle Edits
                $edit = $this->actions['edit'];
                $fields = $edit['fields'];
                $submit = $edit['submit_function'];
                $on_success = $edit['on_success_function'];
                $args = [];
                foreach ($fields as $data) {
                    $field = $data['name'];
                    if (isset($data['type']) && $data['type'] === 'yes-no')
                        $args[$field] = (isset($_POST[$field]) && strtoupper($_POST[$field]) == 'YES')? 1: 0;
                    else {
                        if ($_POST[$field] === '') {
                            echo "<script>alert('All fields are required!')</script>";
                            return false;
                        }
                        $args[$field] = $_POST[$field];
                    }
                }
                $result = call_user_func_array($submit, $args);
                if (!$result) {
                    echo "<script>alert('Failed to save changes to $this->context_name. Check that ID(s) are not already in use.')</script>";
                    return false;
                }
                else
                    $result = call_user_func($on_success);
            }
            if(isset($_POST['delete'])) { // Handle Deletes
                $delete = $this->actions['delete'];
                $submit = $delete['submit_function'];
                $on_success = $delete['on_success_function'];
                $args = [];
                foreach ($delete['fields'] as $field) {
                    $args[$field] = $_POST[$field];
                }
                $result = call_user_func_array($submit, $args);
                if (!$result) {
                    if ($this->context_name === 'student')
                        echo "<script>alert('Failed to delete $this->context_name. Confirm that they are not enrolled in any courses.')</script>";
                    else
                        echo "<script>alert('Failed to delete $this->context_name.')</script>";
                    return false;
                }
                else
                    $result = call_user_func($on_success);
            }
        }
        return $result;
    }
}
?>