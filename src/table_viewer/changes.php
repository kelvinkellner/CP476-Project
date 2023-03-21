<?php
class ChangeManager {
    private $context_name;
    private $add;

    public function __construct($context_name, $actions) {
        $this->context_name = $context_name;
        $this->add = $actions['add'];
    }

    public function show_add() {
        $fields = $this->add['fields'];
        $label = $this->add['label'];
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

    public function check_for_changes() {
        $result = null;
        if($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle actions
            if(isset($_POST['add'])) {
                $fields = $this->add['fields'];
                $submit = $this->add['submit_function'];
                $on_success = $this->add['on_success_function'];
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
                    echo "<script>alert('Failed to add new $this->context_name. Check that ID(s) have not already been used.')</script>";
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