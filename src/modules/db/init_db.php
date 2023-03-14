<?php
// Strict Typing Mode
declare(strict_types = 1);
// Import Root Constants, etc.
include_once(__DIR__.'/../../config/config.php');
?>

<?php
require_once(SITE_ROOT.'/config/private.php');

// Init DB

CONST SQL_CREATE_DB = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
CONST SQL_CREATE_NAME_TABLE = "CREATE TABLE IF NOT EXISTS name (
    student_id INT(9) PRIMARY KEY NOT NULL,
    student_name VARCHAR(30) NOT NULL
)";
CONST SQL_CREATE_COURSE_TABLE = "CREATE TABLE IF NOT EXISTS course (
    student_id INT(9) NOT NULL,
    course_code INT(9) NOT NULL,
    grade_test_1 DOUBLE(5,2),
    grade_test_2 DOUBLE(5,2),
    grade_test_3 DOUBLE(5,2),
    grade_exam DOUBLE(5,2),
    PRIMARY KEY (student_id, course_code)
)";
CONST SQL_CREATE_FINAL_GRADE_TABLE = "CREATE TABLE IF NOT EXISTS final_grade (
    student_id INT(9) NOT NULL,
    student_name VARCHAR(30) NOT NULL,
    course_code INT(9) NOT NULL,
    grade_final DOUBLE(5,2),
    PRIMARY KEY (student_id, course_code)
)";
CONST SQL_CREATE_AUTH_TABLE = "CREATE TABLE IF NOT EXISTS auth (
    user_name VARCHAR(30) NOT NULL,
    id INT(9) PRIMARY KEY,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
CONST SQL_INSERT_DEFAULT_AUTH_USERS = "INSERT INTO auth (user_name, id, is_admin) VALUES " . SQL_DEFAULT_AUTH_USERS;

function connect_to_mysql(): mysqli {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        # SQL variables come from config/private.php
        $conn = new mysqli(HOST, USERNAME, PASSWORD);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4"); # TODO: confirm this is needed
        return $conn;
    } catch(Exception $e) {
        error_log($e->getMessage());
        exit('Error connecting to database');
    }
    return null;
};

function close_connection_to_mysql(mysqli $conn) {
    $conn->close();
};

function create_all_tables(mysqli $conn) {
    try {
        $conn->query(SQL_CREATE_NAME_TABLE);
        $conn->query(SQL_CREATE_COURSE_TABLE);
        $conn->query(SQL_CREATE_FINAL_GRADE_TABLE);
        $conn->query(SQL_CREATE_AUTH_TABLE);
        $conn->query(SQL_INSERT_DEFAULT_AUTH_USERS);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
};

function init_db(): mysqli {
    $conn = connect_to_mysql();
    // Create database, after successful connection create tables
    if ($conn->query(SQL_CREATE_DB) === TRUE) {
        echo "Database created successfully";
        $conn->select_db(DB_NAME);
        create_all_tables($conn);
        close_connection_to_mysql($conn);
    } else {
        echo "Error creating database: " . $conn->error;
    }
    return $conn;
};

?>
