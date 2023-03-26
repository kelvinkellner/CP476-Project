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

// Auth
function auth_user_exists(string $user_name, string $user_id): bool {
    # Check if user exists
    $mysqli = connect_to_db();
    $sql = "SELECT COUNT(*) FROM auth WHERE user_name = ? AND user_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $user_name, $user_id);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_row()[0];
    $mysqli->close();
    if ($count >= 1)
        return true;
    return false;
};
function auth_login(string $user_name, string $user_id): bool {
    # Login or Register automatically
    $user = auth_user_get($user_name, $user_id);
    if ($user) {
        $_SESSION['user'] = $user;
        return true;
    } else {
        if (ALLOW_STUDENTS_TO_LOGIN) {
            # Check if student exists
            $student = student_get($user_id, $user_name);
            if ($student) {
                $_SESSION['user'] = [
                    'user_id' => $student['student_id'],
                    'user_name' => $student['student_name'],
                    'is_admin' => 0
                ];
                $_SESSION['user_is_student'] = true;
                return true;
            }
        }
        # Register new user
        if (AUTO_REGISTER_UNKOWN_AUTH_USERS) {
            $new_user = auth_user_add($user_name, $user_id);
            $_SESSION['user'] = $new_user;
            return true;
        }
    }
    return false;
};
function auth_user_add(string $user_name, string $user_id, int $is_admin = 0) {
    # Check if user already exists
    if (auth_user_exists($user_name, $user_id))
        return false;
    # Add user
    $mysqli = connect_to_db();
    $sql = "INSERT INTO auth (user_name, user_id, is_admin) VALUES (?, ?, ?);";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssi', $user_name, $user_id, $is_admin);
    $stmt->execute();
    $mysqli->close();
    return auth_user_get($user_name, $user_id);
};
function auth_user_delete(string $user_name, string $user_id) {
    # Check that user exists
    if(!auth_user_exists($user_name, $user_id))
        return false;
    # Delete user
    $mysqli = connect_to_db();
    $sql = "DELETE FROM auth WHERE user_name = ? AND user_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $user_name, $user_id);
    $stmt->execute();
    $mysqli->close();
    return true;
};
function auth_user_update(string $og_user_name, string $og_user_id, string $user_name, string $user_id, int $is_admin) {
    # Check that user exists
    if(!auth_user_exists($og_user_name, $og_user_id))
        return false;
    # Check if user ID is already taken
    if($og_user_id != $user_id && auth_user_get($user_name, $user_id))
        return false;
    # Update user
    $mysqli = connect_to_db();
    $sql = "UPDATE auth SET user_name = ?, user_id = ?, is_admin = ? WHERE user_name = ? AND user_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssiss', $user_name, $user_id, $is_admin, $og_user_name, $og_user_id);
    $stmt->execute();
    $mysqli->close();
    return auth_user_get($user_name, $user_id);
};
function auth_user_get(string $user_name, string $user_id): array {
    # Get user object
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM auth WHERE user_name = ? AND user_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $user_name, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $count = $mysqli->affected_rows;
    $mysqli->close();
    if ($count == 1)
        return $user;
    return [];
};
function auth_user_get_all() {
    # Get all users
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM auth;";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $users;
}
function auth_logout() {
    # Logout
    unset($_SESSION['user']);
    unset($_SESSION['cache']);
};
function auth_user_search($user_name='', $user_id='') {
    # Return all users if search is blank (clear search)
    if ($user_name == '' && $user_id == '')
        return auth_user_get_all();
    # Search for users containing user name and/or user id
    $search_user_name = "%$user_name%";
    $search_user_id = "%$user_id%";
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM auth WHERE user_name LIKE ? AND user_id LIKE ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $search_user_name, $search_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $users;
};

// Students
function student_exists($student_id) {
    # Check if student exists
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
function student_add($student_id, $student_name) {
    # Check if student already exists
    if (student_exists($student_id))
        return false;
    # Add student
    $mysqli = connect_to_db();
    $sql = "INSERT INTO name (student_id, student_name) VALUES (?, ?);";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $student_id, $student_name);
    $stmt->execute();
    $mysqli->close();
    return student_get_by_id($student_id);
};
function student_delete($student_id) {
    # Check that student exists
    if(!student_exists($student_id))
        return false;
    # Check that student is not enrolled in any courses
    if(course_get_courses_by_student_id($student_id))
        return false;
    # Delete student
    $mysqli = connect_to_db();
    $sql = "DELETE FROM name WHERE student_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $student_id);
    $stmt->execute();
    $mysqli->close();
    return true;
};
function student_update($og_student_id, $student_id, $student_name) {
    # If student ID is changed...
    if($og_student_id != $student_id) {
        # Check if new student ID is already taken
        if(student_exists($student_id)) 
            return false;
        # Update student ID in course table
        $courses = course_get_courses_by_student_id($og_student_id);
        if($courses)
            course_update_student_id($og_student_id, $student_id);
    }
    # Update student
    $mysqli = connect_to_db();
    $sql = "UPDATE name SET student_id = ?, student_name = ? WHERE student_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sss', $student_id, $student_name, $og_student_id);
    $stmt->execute();
    $mysqli->close();
    # Update final grades
    grade_refresh_final_grades();
    return student_get_by_id($student_id);
};
function student_get($student_id, $student_name) {
    # Get student
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM name WHERE student_id = ? AND student_name = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $student_id, $student_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $count = $mysqli->affected_rows;
    $mysqli->close();
    if ($count == 1)
        return $student;
    return false;
}
function student_get_by_id($student_id) {
    # Get student by id
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM name WHERE student_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $count = $mysqli->affected_rows;
    $mysqli->close();
    if ($count == 1)
        return $student;
    return null;
};
function student_get_all() {
    # Get all students
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM name;";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $students;
};
function student_search($student_id='', $student_name='') {
    # Return all students if search parameters is blank (clear search)
    if ($student_id == '' && $student_name == '')
        return student_get_all();
    # Search for students containing student id and/or student name
    $mysqli = connect_to_db();
    $search_student_id = '%'.$student_id.'%';
    $search_student_name = '%'.$student_name.'%';
    $sql = "SELECT * FROM name WHERE student_id LIKE ? AND student_name LIKE ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $search_student_id, $search_student_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $students;
};

// Courses
function course_get_unique_courses() {
    # Get student enrollment count
    $mysqli = connect_to_db();
    $sql = "SELECT course_code, COUNT(*) AS student_count FROM course GROUP BY course_code ORDER BY course_code;";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $courses;
};
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
function course_get_courses_by_student_id($student_id) {
    # Get courses by student id
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM course WHERE student_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $courses;
};
function course_get_course_by_student_id_and_course_code($student_id, $course_code) {
    # Get course by student id and course code
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM course WHERE student_id = ? AND course_code = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $student_id, $course_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $count = $mysqli->affected_rows;
    $mysqli->close();
    if ($count == 1)
        return $course;
    return null;
};
function course_search_unique_courses($course_code='') {
    # Search for courses containing course code
    $mysqli = connect_to_db();
    $search_course_code = '%'.$course_code.'%';
    $sql = "SELECT course_code, COUNT(*) AS student_count FROM course WHERE course_code LIKE ? GROUP BY course_code ORDER BY course_code;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $search_course_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $courses;
};
function course_update_entry($og_student_id, $og_course_code, $student_id, $course_code, $grade_test_1, $grade_test_2, $grade_test_3, $grade_exam) {
    # Check that entry exists
    if(!course_get_course_by_student_id_and_course_code($og_student_id, $og_course_code))
        return null;
    # Check if duplicate enrolment is already present
    if(($og_student_id != $student_id || $og_course_code != $course_code) && course_get_course_by_student_id_and_course_code($student_id, $course_code))
        return false;
    # Update course entry
    $mysqli = connect_to_db();
    $sql = "UPDATE course SET student_id = ?, course_code = ?, grade_test_1 = ?, grade_test_2 = ?, grade_test_3 = ?, grade_exam = ? WHERE student_id = ? AND course_code = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssiiiiss', $student_id, $course_code, $grade_test_1, $grade_test_2, $grade_test_3, $grade_exam, $og_student_id, $og_course_code);
    $stmt->execute();
    $count = $mysqli->affected_rows;
    $mysqli->close();
    if ($count == 1)
        return true;
    return false;
};
function course_update_student_id($og_student_id, $student_id) {
    # Check that entries exist
    if(!course_get_courses_by_student_id($og_student_id))
        return null;
    # Change student id in all course entries
    $mysqli = connect_to_db();
    $sql = "UPDATE course SET student_id = ? WHERE student_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $student_id, $og_student_id);
    $stmt->execute();
    $count = $mysqli->affected_rows;
    $mysqli->close();
    if ($count > 0)
        return true;
    return false;
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
function grade_get_grades_by_student_id($student_id) {
    # Get grades by student id
    $mysqli = connect_to_db();
    $sql = "SELECT * FROM final_grade WHERE student_id = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $grades = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $grades;
};
function grade_search($student_id='', $student_name='', $course_code='') {
    # Return all grades if search is blank (clear search)
    if ($student_id == '' && $student_name == '' && $course_code == '')
        return grade_get_all();
    # Search for grades containing student id, student name, and/or course code
    $mysqli = connect_to_db();
    $search_student_id = '%'.$student_id.'%';
    $search_student_name = '%'.$student_name.'%';
    $search_course_code = '%'.$course_code.'%';
    $sql = "SELECT * FROM final_grade WHERE student_id LIKE ? AND student_name LIKE ? AND course_code LIKE ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sss', $search_student_id, $search_student_name, $search_course_code);
    $stmt->execute();
    $grades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $grades;
};
function grade_get_by_student_id_search_by_course_code($student_id, $course_code) {
    # Return null if student id is blank
    if ($student_id == '')
        return null;
    # Return all grades for student if search is blank (clear search)
    if ($course_code == '')
        return grade_get_grades_by_student_id($student_id);
    # Search for grades containing course code
    $mysqli = connect_to_db();
    $search_course_code = '%'.$course_code.'%';
    $sql = "SELECT * FROM final_grade WHERE student_id = ? AND course_code LIKE ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $student_id, $search_course_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $grades = $result->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    return $grades;    
}
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
function grade_add_entry($student_id, $student_name, $course_code, $grade_test_1, $grade_test_2, $grade_test_3, $grade_exam) {
    # Check that student exists
    if(!student_exists($student_id)) {
        # Add new student
        if (!student_add($student_id, $student_name))
            return null;
    }
    # Check if duplicate enrolment is already present
    else if (course_get_course_by_student_id_and_course_code($student_id, $course_code))
        return false;
    # Add entry to course table
    $mysqli = connect_to_db();
    $sql = "INSERT INTO course (student_id, course_code, grade_test_1, grade_test_2, grade_test_3, grade_exam) VALUES (?, ?, ?, ?, ?, ?);";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssiiii', $student_id, $course_code, $grade_test_1, $grade_test_2, $grade_test_3, $grade_exam);
    $stmt->execute();
    $mysqli->close();
    # Refresh grades table
    return grade_refresh_final_grades();
};
function grade_update_entry($og_student_id, $og_course_code, $student_id, $student_name, $course_code, $grade_test_1, $grade_test_2, $grade_test_3, $grade_exam) {
    # Update course entry
    if(!course_update_entry($og_student_id, $og_course_code, $student_id, $course_code, $grade_test_1, $grade_test_2, $grade_test_3, $grade_exam))
        return null;
    # Refresh grades table
    return grade_refresh_final_grades();
};
function grade_delete_entry($student_id, $course_code) {
    # Check that entry exists
    if (!course_get_course_by_student_id_and_course_code($student_id, $course_code))
        return false;
    # Delete entry from course table
    $mysqli = connect_to_db();
    $sql = "DELETE FROM course WHERE student_id = ? AND course_code = ?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $student_id, $course_code);
    $stmt->execute();
    $mysqli->close();
    # Refresh grades table
    return grade_refresh_final_grades();
};

?>

<?php
include_once(__DIR__.'/../templates/footer.php');
?>