<?php
// Strict Typing Mode
declare(strict_types = 1);
// Import Root Constants, etc.
include_once(__DIR__.'/../../config/config.php');
?>

<?php
require_once(SITE_ROOT.'/config/private.php');

// Auth
function auth_login() {};
function auth_user_add() {};
function auth_user_delete() {};
function auth_user_update() {};
function auth_user_get() {};
function auth_logout() {};

// Students
function student_add() {};
function student_delete() {};
function student_update() {};
function student_get() {};
function student_get_all() {};

// Courses
function course_add() {};
function course_delete() {};
function course_update() {};
function course_get() {};
function course_get_all() {};
function course_get_courses_by_student_id() {};
function course_get_students_by_course_code() {};
function course_get_course_by_student_id_and_course_code() {};

// Grades
function grade_add() {};
function grade_delete() {};
function grade_update() {};
function grade_get() {};
function grade_get_all() {};
function grade_get_grades_by_student_id() {};
function grade_get_grades_by_course_code() {};
function grade_get_grades_by_student_id_and_course_code() {};

?>
