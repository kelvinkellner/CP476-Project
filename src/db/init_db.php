<?php
require_once(__DIR__.'/../../private.php');

function parse_file(string $file_path): array {
    $file = fopen($file_path, 'r');
    $data = [];
    while (($line = fgets($file)) !== FALSE)
        $data[] = explode(', ', $line);
        foreach ($data as $key => $value)
            $data[$key] = array_map('trim', $value);
    fclose($file);
    return $data;
}

// Init DB

CONST SQL_CREATE_DB = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
CONST SQL_USE_DB = "USE " . DB_NAME;
CONST SQL_CREATE_NAME_TABLE = "CREATE TABLE IF NOT EXISTS name (
    student_id INT(9) PRIMARY KEY NOT NULL,
    student_name VARCHAR(30) NOT NULL
)";
CONST SQL_CREATE_COURSE_TABLE = "CREATE TABLE IF NOT EXISTS course (
    student_id INT(9) NOT NULL,
    course_code VARCHAR(5) NOT NULL,
    grade_test_1 INT(3),
    grade_test_2 INT(3),
    grade_test_3 INT(3),
    grade_exam INT(3),
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
    mod_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_name, user_id)
)";
CONST SQL_TABLE_EXISTS = "SELECT 1 FROM information_schema.tables WHERE table_schema = database() AND table_name = ?";
CONST SQL_DROP_ALL_TABLES = "DROP TABLE IF EXISTS name, course, final_grade, auth";
CONST SQL_INSERT_DEFAULT_AUTH_USERS = "INSERT INTO auth (user_name, user_id, is_admin) VALUES (?, ?, ?)";
CONST SQL_INSERT_DEFAULT_NAMES = "INSERT INTO name (student_id, student_name) VALUES (?, ?)";
CONST SQL_INSERT_DEFAULT_COURSES = "INSERT INTO course (student_id, course_code, grade_test_1, grade_test_2, grade_test_3, grade_exam) VALUES (?, ?, ?, ?, ?, ?)";
CONST SQL_CLEAR_FINAL_GRADES = "DELETE FROM final_grade";
CONST SQL_POPULATE_FINAL_GRADES = "INSERT INTO final_grade (student_id, student_name, course_code, grade_final) SELECT course.student_id, name.student_name, course.course_code, (course.grade_test_1 + course.grade_test_2 + course.grade_test_3 + course.grade_exam) / 4 AS grade_final FROM course INNER JOIN name ON course.student_id = name.student_id";

function connect_to_mysql(): PDO {
    try {
        # SQL variables come from private.php
        $conn = new PDO("mysql:host=".HOST, USERNAME, PASSWORD);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        error_log($e->getMessage());
        exit('Error connecting to database for initialization: '.$e->getMessage());
    }
    return null;
};

function create_all_tables(PDO $conn) {
    $tables_created = [];
    try {
        // Initialize all DB tables
        $sql_exists =  $conn->prepare(SQL_TABLE_EXISTS);
        $queue = [
            'name' => SQL_CREATE_NAME_TABLE,
            'course' => SQL_CREATE_COURSE_TABLE,
            'final_grade' => SQL_CREATE_FINAL_GRADE_TABLE,
            'auth' => SQL_CREATE_AUTH_TABLE
        ];
        // Create each table if it doesn't exist
        foreach ($queue as $table_name => $sql_create) {
            $sql_exists->execute([$table_name]);
            $exists = (bool)$sql_exists->fetchColumn();
            if(!$exists) {
                $conn->exec($sql_create);
                $tables_created[$table_name] = true;
            }
        }
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        $tables_created['auth'] = true;
        // return [];
    }
    return $tables_created;
};

function fill_default_values(PDO $conn, array $tables_created) {
    try {
        // Fill database with default values if tables were just created
        if (array_key_exists('name', $tables_created)) {
            $default_names = parse_file(__DIR__.'/defaults/name.txt');
            $stmt = $conn->prepare(SQL_INSERT_DEFAULT_NAMES);
            foreach($default_names as $name)
                $stmt->execute($name);
        }
        if (array_key_exists('course', $tables_created)) {
            $default_courses = parse_file(__DIR__.'/defaults/course.txt');
            $stmt = $conn->prepare(SQL_INSERT_DEFAULT_COURSES);
            foreach($default_courses as $course)
                $stmt->execute($course);
        }
        if (array_key_exists('auth', $tables_created)) {
            $default_auth_users = parse_file(__DIR__.'/defaults/auth.txt');
            $stmt = $conn->prepare(SQL_INSERT_DEFAULT_AUTH_USERS);
            foreach($default_auth_users as $user)
                $stmt->execute($user);
        }
        # Calculate final grades
        refresh_final_grades($conn);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

function refresh_final_grades(PDO $conn) {
    try {
        // Delete old final grades
        $conn->exec(SQL_CLEAR_FINAL_GRADES);
        // Re-calcute final grades and populate table for all students per course
        $conn->exec(SQL_POPULATE_FINAL_GRADES);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

function init_db(): bool {
    $conn = connect_to_mysql();
    // Create database, after successful connection create tables
    if ($conn->exec(SQL_CREATE_DB)) {
        $conn->exec(SQL_USE_DB);
        $tables_created = create_all_tables($conn);
        if(!empty($tables_created))
            fill_default_values($conn, $tables_created);
        $conn = null;
        return true;
    }
    $conn = null;
    return false;
};

function reset_db(): void {
    $conn = connect_to_mysql();
    $conn->exec(SQL_USE_DB);
    $conn->exec(SQL_DROP_ALL_TABLES);
    $conn = null;
};

?>