<?php
include_once(__DIR__.'/../templates/head.php');
?>

<?php
require_once(__DIR__.'/../../private.php');

// Auth
function auth_login(string $user_name, string $user_id): bool {
    # Login or Register automatically
    $user = auth_user_get($user_name, $user_id);
    if ($user) {
        $_SESSION['user'] = $user;
        return true;
    } else {
        $new_user = auth_user_add($user_name, $user_id);
        $_SESSION['user'] = $new_user;
        return true;
    }
    return false;
};
function auth_user_add(string $user_name, string $user_id, int $is_admin = 0) {
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    # Check if user already exists
    $sql = "SELECT * FROM auth WHERE user_id = '$user_id'";
    $result = $conn->query($sql);
    $count = $result->num_rows;
    if ($count >= 1)
        return false;
    # Add user
    $sql = "INSERT INTO auth (user_name, user_id, is_admin) VALUES ('$user_name', '$user_id', '$is_admin')";
    $conn->query($sql);
    $conn->close();
    return auth_user_get($user_name, $user_id);
};
function auth_user_delete(string $user_name, string $user_id) {
    # Delete user
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "DELETE FROM auth WHERE user_name = '$user_name' AND user_id = '$user_id'";
    $conn->query($sql);
    $conn->close();
};
function auth_user_update(string $user_name, string $user_id, int $is_admin) {
    # Update user's admin status
    if ($is_admin < 0 || $is_admin > 1)
        return false;
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "UPDATE auth SET is_admin = '$is_admin' WHERE user_name = '$user_name' AND user_id = '$user_id'";
    $conn->query($sql);
    $conn->close();
    return true;
};
function auth_user_get(string $user_name, string $user_id): array {
    # Get user's info
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT * FROM auth WHERE user_name = '$user_name' AND user_id = '$user_id'";
    $result = $conn->query($sql);
    $count = $result->num_rows;
    $user = $result->fetch_assoc();
    $conn->close();
    if ($count == 1)
        return $user;
    return [];
};
function auth_user_get_all() {
    # Get all users
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT * FROM auth";
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc())
        array_push($users, $row);
    $conn->close();
    return $users;
}
function auth_logout() {
    # Logout
    unset($_SESSION['user']);
};
function auth_user_search($user_name='', $user_id='') {
    if ($user_name == '' && $user_id == '')
        return auth_user_get_all();
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT * FROM auth WHERE user_name LIKE '%$user_name%' AND user_id LIKE '%$user_id%'";
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc())
        array_push($users, $row);
    $conn->close();
    return $users;
};

// Students
function student_exists() {};
function student_add() {};
function student_delete() {};
function student_update() {};
function student_get_by_id($student_id) {
    # Get student by id
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT * FROM name WHERE student_id = '$student_id'";
    $result = $conn->query($sql);
    $count = $result->num_rows;
    $student = $result->fetch_assoc();
    $conn->close();
    if ($count == 1)
        return $student;
    return null;
};
function student_get_all() {
    # Get all students
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT * FROM name";
    $result = $conn->query($sql);
    $students = [];
    while ($row = $result->fetch_assoc())
        array_push($students, $row);
    $conn->close();
    return $students;
};
function student_search($student_id='', $student_name='') {
    if ($student_id == '' && $student_name == '')
        return student_get_all();
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT * FROM name WHERE student_id LIKE '%$student_id%' AND student_name LIKE '%$student_name%'";
    $result = $conn->query($sql);
    $students = [];
    while ($row = $result->fetch_assoc())
        array_push($students, $row);
    $conn->close();
    return $students;
};

// Courses
function course_exists() {};
function course_add() {};
function course_delete() {};
function course_update() {};
function course_get_unique_courses() {
    # Get student enrollment count
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT course_code, COUNT(*) AS student_count FROM course GROUP BY course_code";
    $result = $conn->query($sql);
    $courses = [];
    while ($row = $result->fetch_assoc())
        array_push($courses, $row);
    $conn->close();
    return $courses;
};
function course_get_all() {
    # Get all courses
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT * FROM course";
    $result = $result = $conn->query($sql);
    $courses = [];
    while ($row = $result->fetch_assoc())
        array_push($courses, $row);
    $conn->close();
    return $courses;
};
function course_get_courses_by_student_id($student_id) {
    # Get courses by student id
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT * FROM course WHERE student_id = '$student_id'";
    $result = $conn->query($sql);
    $courses = [];
    while ($row = $result->fetch_assoc())
        array_push($courses, $row);
    $conn->close();
    return $courses;
};
function course_get_students_by_course_code() {};
function course_get_course_by_student_id_and_course_code() {};
function course_search_unique_courses($course_code='') {
    if ($course_code == '')
        return course_get_unique_courses();
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT course_code, COUNT(*) AS student_count FROM course WHERE course_code LIKE '%$course_code%' GROUP BY course_code";
    $result = $conn->query($sql);
    $courses = [];
    while ($row = $result->fetch_assoc())
        array_push($courses, $row);
    $conn->close();
    return $courses;
};

// Grades
function grade_add() {};
function grade_delete() {};
function grade_update() {};
function grade_get() {};
function grade_get_all() {
    # Get all final grades
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT * FROM final_grade";
    $result = $conn->query($sql);
    $grades = [];
    while ($row = $result->fetch_assoc())
        array_push($grades, $row);
    $conn->close();
    return $grades;
};
function grade_get_grades_by_student_id() {};
function grade_get_grades_by_course_code() {};
function grade_get_grades_by_student_id_and_course_code() {};
function grade_search($student_id='', $student_name='', $course_code='') {
    # Get matching grades
    if ($student_id == '' && $student_name == '' && $course_code == '')
        return grade_get_all();
    $conn = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
    $sql = "SELECT * FROM final_grade WHERE student_id LIKE '%$student_id%' AND student_name LIKE '%$student_name%' AND course_code LIKE '%$course_code%'";
    $result = $conn->query($sql);
    $grades = [];
    while ($row = $result->fetch_assoc())
        array_push($grades, $row);
    $conn->close();
    return $grades;
};

?>

<?php
include_once(__DIR__.'/../templates/footer.php');
?>