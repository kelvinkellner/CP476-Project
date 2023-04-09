<?php
class ChangeManager {
    private $context_name;
    private $actions;

    public function __construct($context_name, $actions) {
        $this->context_name = $context_name;
        $this->actions = $actions;
    }

    public function show_edit() {
        echo "<input type='submit' name='edit' value='Save Changes'>";
    }

    public function check_for_changes() {
        $result = null;
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        }
        return $result;
    }
}
?>