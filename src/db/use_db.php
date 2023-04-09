<?php
include_once(__DIR__.'/../templates/head.php');
?>

<?php
require_once(__DIR__.'/../../private.php');
require_once(__DIR__.'/../../config.php');

// MySQL overhead
function connect_to_db(): mysqli {
    $mysqli = mysqli_init();
    if (!$mysqli)
        die('mysqli_init failed');
    if (!$mysqli->real_connect(HOST, USERNAME, PASSWORD, DB_NAME, null, null, MYSQLI_CLIENT_FOUND_ROWS))
        die('Connect Error ('.mysqli_connect_errno().') '.mysqli_connect_error());
    // echo 'Success... ' . $mysqli->host_info . "\n";
    return $mysqli;
}

// Names
function name_get_all() {
    # Get all name
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM name;";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $names = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $names;
};
function name_exists($student_id) {
    # Check if name exists
    $mysqli = connect_to_db();
    $sql = "SELECT COUNT(*) FROM name WHERE student_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $student_id);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_row()[0];
    $mysqli->close();
    if ($count >= 1)
        return true;
    return false;
};
function name_update($og_student_id, $student_id, $student_name) {
    # If student ID is changed...
    if($og_student_id != $student_id) {
        # Check if new student ID is already taken
        if(name_exists($student_id)) 
            return false;
    }
    # Update name
    $mysqli = connect_to_db();
    $sql = "UPDATE name SET student_id = ?, student_name = ? WHERE student_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sss', $student_id, $student_name, $og_student_id);
    $stmt->execute();
    $mysqli->close();
    # Update final grades
    grade_refresh_final_grades();
    return true;
};

// Courses
function course_get_all() {
    # Get all courses
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM course;";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $courses;
};
function course_exists($student_id, $course_code) {
    # Check if course exists
    $mysqli = connect_to_db();
    $sql = "SELECT COUNT(*) FROM course WHERE student_id = ? AND course_code = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $student_id, $course_code);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_row()[0];
    $mysqli->close();
    if ($count >= 1)
        return true;
    return false;
};
function course_update($og_student_id, $og_course_code, $student_id, $course_code, $grade_test_1, $grade_test_2, $grade_test_3, $grade_exam) {
    # If student ID or course code is changed...
    if($og_student_id != $student_id || $og_course_code != $course_code) {
        # Check if new student ID and course code is already taken
        if(course_exists($student_id, $course_code)) 
            return false;
    }
    # Update course
    $mysqli = connect_to_db();
    $sql = "UPDATE course SET student_id = ?, course_code = ?, grade_test_1 = ?, grade_test_2 = ?, grade_test_3 = ?, grade_exam = ? WHERE student_id = ? AND course_code = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssiiiiis', $student_id, $course_code, $grade_test_1, $grade_test_2, $grade_test_3, $grade_exam, $og_student_id, $og_course_code);
    $stmt->execute();
    $mysqli->close();
    # Update final grades
    grade_refresh_final_grades();
    return true;
};

// Grades
function grade_get_all() {
    # Get all grades
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM final_grade;";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $grades = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $grades;
};
function grade_refresh_final_grades() {
    # Clear all grades
    $mysqli = connect_to_db();
    $sql = "DELETE FROM final_grade;";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    # Re-calculate all final grades
    $sql = "INSERT INTO final_grade (student_id, student_name, course_code, grade_final) SELECT course.student_id, name.student_name, course.course_code, (course.grade_test_1 + course.grade_test_2 + course.grade_test_3 + course.grade_exam) / 4 AS grade_final FROM course INNER JOIN name ON course.student_id = name.student_id;";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $mysqli->close();
    return true;
};
?>

<?php
include_once(__DIR__.'/../templates/footer.php');
?>