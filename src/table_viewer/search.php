<?php
class SearchBar {
    private $fields;
    private $context_name;
    private $search;

    public function __construct(array $fields, string $context_name, $search_function) {
        $this->fields = $fields;
        $this->context_name = $context_name;
        $this->search = $search_function;
    }

    public function show() {
        echo "<form id='search' method='post'>";
        echo "<input type='hidden' name='$this->context_name' value='true'>";
        echo "<label><strong>Search</strong></label><br/>";
        foreach ($this->fields as $field => $label) {
            echo "<input type='text' name='$field' placeholder='$label' value=";
            echo array_key_exists("search_".$field, $_SESSION)? $_SESSION["search_".$field]: '';
            echo ">";
        }
        echo "<input type='submit' name='search' value='Search'>";
        echo "<input type='submit' name='clear' value='Clear Filters'>";
        echo "</form>";
    }

    public function check_for_searches() {
        $result = null;
        if($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle actions
            if(isset($_POST['search'])) {
                $args = [];
                foreach ($this->fields as $field => $label) {
                    $_SESSION["search_".$field] = $_POST[$field];
                    $args[$field] = $_POST[$field];
                }
                $result = call_user_func_array($this->search, $args);
            }
            if(isset($_POST['clear'])) {
                $args = [];
                foreach ($this->fields as $field => $label) {
                    unset($_SESSION["search_".$field]);
                    $args[$field] = '';
                }
                $result = call_user_func_array($this->search, $args);
            }
        }
        return $result;
    }
}
?>