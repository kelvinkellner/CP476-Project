<?php
include_once(__DIR__.'/../templates/head.php');
?>

<?php
require_once(__DIR__.'/../../private.php');
require_once(__DIR__.'/../../config.php');

// Auth
function auth_user_exists(string $user_id, string $user_name): bool {
    # Check if user exists
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM auth WHERE user_id = :user_id AND user_name = :user_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'user_name' => $user_name]);
    $count = $stmt->rowCount();
    $pdo = null;
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
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "INSERT INTO auth (user_name, user_id, is_admin) VALUES (:user_name, :user_id, :is_admin)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_name' => $user_name, 'user_id' => $user_id, 'is_admin' => $is_admin]);
    $pdo = null;
    return auth_user_get($user_name, $user_id);
};
function auth_user_delete(string $user_name, string $user_id) {
    # Check that user exists
    if(!auth_user_exists($user_name, $user_id))
        return false;
    # Delete user
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "DELETE FROM auth WHERE user_name = :user_name AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_name' => $user_name, 'user_id' => $user_id]);
    $pdo = null;
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
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "UPDATE auth SET user_name = :user_name, user_id = :user_id, is_admin = :is_admin WHERE user_name = :og_user_name AND user_id = :og_user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_name' => $user_name, 'user_id' => $user_id, 'is_admin' => $is_admin, 'og_user_name' => $og_user_name, 'og_user_id' => $og_user_id]);
    $pdo = null;
    return auth_user_get($user_name, $user_id);
};
function auth_user_get(string $user_name, string $user_id): array {
    # Get user object
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM auth WHERE user_name = :user_name AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_name' => $user_name, 'user_id' => $user_id]);
    $count = $stmt->rowCount();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $pdo = null;
    if ($count == 1)
        return $user;
    return [];
};
function auth_user_get_all() {
    # Get all users
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM auth";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
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
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM auth WHERE user_name LIKE :user_name AND user_id LIKE :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_name' => $search_user_name, 'user_id' => $search_user_id]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    return $users;
};

// Students
function student_exists($student_id) {
    # Check if student exists
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM name WHERE student_id = :student_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id]);
    $count = $stmt->rowCount();
    $pdo = null;
    if ($count >= 1)
        return true;
    return false;
};
function student_add($student_id, $student_name) {
    # Check if student already exists
    if (student_exists($student_id))
        return false;
    # Add student
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "INSERT INTO name (student_id, student_name) VALUES (:student_id, :student_name)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id, 'student_name' => $student_name]);
    $pdo = null;
    return student_get_by_id($student_id);
};
function student_delete($student_id) {
    # Check that student exists
    if(!student_exists($student_id))
        return false;
    # Delete student
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "DELETE FROM name WHERE student_id = :student_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id]);
    $pdo = null;
    return true;
};
function student_update($og_student_id, $student_id, $student_name) {
    # Check that student exists
    if(!student_exists($student_id))
        return false;
    # Check if student ID is already taken
    if($og_student_id != $student_id && student_get_by_id($student_id))
        return false;
    # Update student
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "UPDATE name SET student_id = :student_id, student_name = :student_name WHERE student_id = :og_student_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id, 'student_name' => $student_name, 'og_student_id' => $og_student_id]);
    $pdo = null;
    return student_get_by_id($student_id);
};
function student_get($student_id, $student_name) {
    # Get student
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM name WHERE student_id = :student_id AND student_name = :student_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id, 'student_name' => $student_name]);
    $count = $stmt->rowCount();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    $pdo = null;
    if ($count == 1)
        return $student;
    return false;
}
function student_get_by_id($student_id) {
    # Get student by id
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM name WHERE student_id = :student_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id]);
    $count = $stmt->rowCount();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    $pdo = null;
    if ($count == 1)
        return $student;
    return null;
};
function student_get_all() {
    # Get all students
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    return $students;
};
function student_search($student_id='', $student_name='') {
    # Return all students if search parameters is blank (clear search)
    if ($student_id == '' && $student_name == '')
        return student_get_all();
    # Search for students containing student id and/or student name
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $search_student_id = '%'.$student_id.'%';
    $search_student_name = '%'.$student_name.'%';
    $sql = "SELECT * FROM name WHERE student_id LIKE :student_id AND student_name LIKE :student_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $search_student_id, 'student_name' => $search_student_name]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    return $students;
};

// Courses
function course_get_unique_courses() {
    # Get student enrollment count
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT course_code, COUNT(*) AS student_count FROM course GROUP BY course_code";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    return $courses;
};
function course_get_all() {
    # Get all courses
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM course";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    return $courses;
};
function course_get_courses_by_student_id($student_id) {
    # Get courses by student id
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM course WHERE student_id = :student_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    return $courses;
};
function course_get_course_by_student_id_and_course_code($student_id, $course_code) {
    # Get course by student id and course code
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM course WHERE student_id = :student_id AND course_code = :course_code";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id, 'course_code' => $course_code]);
    $count = $stmt->rowCount();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    $pdo = null;
    if ($count == 1)
        return $course;
    return null;
};
function course_search_unique_courses($course_code='') {
    # Search for courses containing course code
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $search_course_code = '%'.$course_code.'%';
    $sql = "SELECT course_code, COUNT(*) AS student_count FROM course WHERE course_code LIKE :course_code GROUP BY course_code";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['course_code' => $search_course_code]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
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
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "UPDATE course SET student_id = :student_id, course_code = :course_code, grade_test_1 = :grade_test_1, grade_test_2 = :grade_test_2, grade_test_3 = :grade_test_3, grade_exam = :grade_exam WHERE student_id = :og_student_id AND course_code = :og_course_code";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id, 'course_code' => $course_code, 'grade_test_1' => $grade_test_1, 'grade_test_2' => $grade_test_2, 'grade_test_3' => $grade_test_3, 'grade_exam' => $grade_exam, 'og_student_id' => $og_student_id, 'og_course_code' => $og_course_code]);
    $count = $stmt->rowCount();
    $pdo = null;
    if ($count == 1)
        return true;
    return false;
};

// Grades
function grade_get_all() {
    # Get all grades
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM final_grade";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    return $grades;
};
function grade_get_grades_by_student_id($student_id) {
    # Get grades by student id
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "SELECT * FROM final_grade WHERE student_id = :student_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id]);
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    return $grades;
};
function grade_search($student_id='', $student_name='', $course_code='') {
    # Return all grades if search is blank (clear search)
    if ($student_id == '' && $student_name == '' && $course_code == '')
        return grade_get_all();
    # Search for grades containing student id, student name, and/or course code
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $search_student_id = '%'.$student_id.'%';
    $search_student_name = '%'.$student_name.'%';
    $search_course_code = '%'.$course_code.'%';
    $sql = "SELECT * FROM final_grade WHERE student_id LIKE :student_id AND student_name LIKE :student_name AND course_code LIKE :course_code";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $search_student_id, 'student_name' => $search_student_name, 'course_code' => $search_course_code]);
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
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
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $search_course_code = '%'.$course_code.'%';
    $sql = "SELECT * FROM final_grade WHERE student_id = :student_id AND course_code LIKE :course_code";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id, 'course_code' => $search_course_code]);
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pdo = null;
    return $grades;    
}
function grade_refresh_final_grades() {
    # Clear all grades
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "DELETE FROM final_grade";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    # Re-calculate all final grades
    $sql = "INSERT INTO final_grade (student_id, student_name, course_code, grade_final) SELECT course.student_id, name.student_name, course.course_code, (course.grade_test_1 + course.grade_test_2 + course.grade_test_3 + course.grade_exam) / 4 AS grade_final FROM course INNER JOIN name ON course.student_id = name.student_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $pdo = null;
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
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "INSERT INTO course (student_id, course_code, grade_test_1, grade_test_2, grade_test_3, grade_exam) VALUES (:student_id, :course_code, :grade_test_1, :grade_test_2, :grade_test_3, :grade_exam)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id, 'course_code' => $course_code, 'grade_test_1' => $grade_test_1, 'grade_test_2' => $grade_test_2, 'grade_test_3' => $grade_test_3, 'grade_exam' => $grade_exam]);
    $pdo = null;
    # Refresh grades table
    return grade_refresh_final_grades();
};
function grade_update_entry($og_student_id, $og_course_code, $student_id, $student_name, $course_code, $grade_test_1, $grade_test_2, $grade_test_3, $grade_exam) {
    # Check that student exists
    if(!student_exists($og_student_id)) {
        # Add new student
        if (!student_add($student_id, $student_name))
            return null;
    }
    # Update course entry
    else if(!course_update_entry($og_student_id, $og_course_code, $student_id, $course_code, $grade_test_1, $grade_test_2, $grade_test_3, $grade_exam))
        return null;
    # Refresh grades table
    return grade_refresh_final_grades();
};
function grade_delete_entry($student_id, $course_code) {
    # Check that entry exists
    if (!course_get_course_by_student_id_and_course_code($student_id, $course_code))
        return false;
    # Delete entry from course table
    $pdo = new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USERNAME, PASSWORD);
    $sql = "DELETE FROM course WHERE student_id = :student_id AND course_code = :course_code";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['student_id' => $student_id, 'course_code' => $course_code]);
    $pdo = null;
    # Refresh grades table
    return grade_refresh_final_grades();
};

?>

<?php
include_once(__DIR__.'/../templates/footer.php');
?>