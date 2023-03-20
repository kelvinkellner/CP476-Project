<?php
require_once(__DIR__.'/../../private.php');

function parse_file(string $file_path): array {
    $file = fopen($file_path, 'r');
    $data = [];
    while (($line = fgets($file)) !== FALSE) {
        $data[] = explode(', ', $line);
    }
    fclose($file);
    return $data;
}

// Init DB

CONST SQL_CREATE_DB = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
CONST SQL_CREATE_NAME_TABLE = "CREATE TABLE IF NOT EXISTS name (
    student_id INT(9) PRIMARY KEY NOT NULL,
    student_name VARCHAR(30) NOT NULL
)";
CONST SQL_CREATE_COURSE_TABLE = "CREATE TABLE IF NOT EXISTS course (
    student_id INT(9) NOT NULL,
    course_code VARCHAR(5) NOT NULL,
    grade_test_1 DOUBLE(5,2),
    grade_test_2 DOUBLE(5,2),
    grade_test_3 DOUBLE(5,2),
    grade_exam DOUBLE(5,2),
    PRIMARY KEY (student_id, course_code)
)";
CONST SQL_CREATE_FINAL_GRADE_TABLE = "CREATE TABLE IF NOT EXISTS final_grade (
    student_id INT(9) NOT NULL,
    student_name VARCHAR(30) NOT NULL,
    course_code VARCHAR(5) NOT NULL,
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
CONST SQL_DROP_FINAL_GRADES = "DROP TABLE IF EXISTS final_grade";
CONST SQL_DROP_ALL_TABLES = "DROP TABLE IF EXISTS name, course, final_grade, auth";
CONST SQL_INSERT_DEFAULT_AUTH_USERS = "INSERT INTO auth (user_name, user_id, is_admin) VALUES (?, ?, ?)";
CONST SQL_INSERT_DEFAULT_NAMES = "INSERT INTO name (student_id, student_name) VALUES (?, ?)";
CONST SQL_INSERT_DEFAULT_COURSES = "INSERT INTO course (student_id, course_code, grade_test_1, grade_test_2, grade_test_3, grade_exam) VALUES (?, ?, ?, ?, ?, ?)";
CONST SQL_POPULATE_FINAL_GRADES = "INSERT INTO final_grade (student_id, student_name, course_code, grade_final) SELECT course.student_id, name.student_name, course.course_code, (course.grade_test_1 + course.grade_test_2 + course.grade_test_3 + course.grade_exam) / 4 AS grade_final FROM course INNER JOIN name ON course.student_id = name.student_id";

function connect_to_mysql(): PDO {
    try {
        # SQL variables come from config/private.php
        $conn = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        error_log($e->getMessage());
        exit('Error connecting to database');
    }
    return null;
};

function create_all_tables(PDO $conn) {
    try {
        // Initialize all DB tables
        $conn->exec(SQL_CREATE_NAME_TABLE);
        $conn->exec(SQL_CREATE_COURSE_TABLE);
        $conn->exec(SQL_CREATE_FINAL_GRADE_TABLE);
        $conn->exec(SQL_CREATE_AUTH_TABLE);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
};

function fill_default_values(PDO $conn) {
    try {
        // Fill database with all default values
        $default_auth_users = parse_file(__DIR__.'/defaults/authFile.txt');
        $default_courses = parse_file(__DIR__.'/defaults/courseFile.txt');
        $default_names = parse_file(__DIR__.'/defaults/nameFile.txt');
        $stmt_user = $conn->prepare(SQL_INSERT_DEFAULT_AUTH_USERS);
        $stmt_course = $conn->prepare(SQL_INSERT_DEFAULT_COURSES);
        $stmt_name = $conn->prepare(SQL_INSERT_DEFAULT_NAMES);
        foreach($default_auth_users as $user) $stmt_user->execute($user);
        foreach($default_courses as $course) $stmt_course->execute($course);
        foreach($default_names as $name) $stmt_name->execute($name);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

function populate_final_grades(PDO $conn) {
    try {
        // Populate final grade table for all students per course
        $conn->exec(SQL_POPULATE_FINAL_GRADES);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
};

function init_db(): bool {
    $conn = connect_to_mysql();
    // Create database, after successful connection create tables
    if ($conn->exec(SQL_CREATE_DB)) {
        echo "HELLLOOOO";
        // echo "Database created successfully"; // TODO: remove or replace at some point
        $conn->exec(SQL_DROP_ALL_TABLES); // TODO: remove when finished testing
        create_all_tables($conn);
        fill_default_values($conn);
        populate_final_grades($conn);
        $conn = null;
        return true;
    }
    return false;
};

?>
