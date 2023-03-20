<?php
require_once(__DIR__.'/../../private.php');

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
    user_id INT(9),
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_name, user_id)
)";
CONST SQL_COUNT_AUTH_USERS = "SELECT COUNT(*) FROM auth";
// CONST SQL_INSERT_DEFAULT_AUTH_USERS = "INSERT INTO auth (user_name, user_id, is_admin) VALUES " . SQL_DEFAULT_AUTH_USERS;
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
CONST SQL_DROP_ALL_TABLES = "DROP TABLE IF EXISTS name, course, final_grade, auth";

function close_connection_to_mysql(mysqli $conn) {
    $conn->close();
};

function create_all_tables(mysqli $conn) {
    try {
        // Initialize all DB tables
        $conn->query(SQL_CREATE_NAME_TABLE);
        $conn->query(SQL_CREATE_COURSE_TABLE);
        $conn->query(SQL_CREATE_FINAL_GRADE_TABLE);
        $conn->query(SQL_CREATE_AUTH_TABLE);
        $num_auth_users = $conn->query(SQL_COUNT_AUTH_USERS)->fetch_array()[0];
        if ($num_auth_users < 1) {
            // $conn->query(SQL_INSERT_DEFAULT_AUTH_USERS);
        }
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
};

function init_db(): mysqli {
    $conn = connect_to_mysql();
    // Create database, after successful connection create tables
    if ($conn->query(SQL_CREATE_DB) === TRUE) {
        // echo "Database created successfully"; // TODO: remove or replace at some point
        $conn->select_db(DB_NAME);
        // $conn->query(SQL_DROP_ALL_TABLES); // TODO: remove when finished testing
        create_all_tables($conn);
        close_connection_to_mysql($conn);
    } else {
        echo "Error creating database: " . $conn->error;
    }
    return $conn;
};

?>
