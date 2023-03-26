# CP476 Final Project
### A simple grading app for teachers and students.

This web app is built using PHP and runs on an Apache web server.
Instructors can view/update grades and registration information,
and students can login to view their grades in all their courses.

## Installation

1. Install [PHP](https://windows.php.net/qa/) and [Apache](https://www.apachelounge.com/download/).
2. Clone this repository directly into your Apache web server's root directory ('C:\Apache24\htdocs\' on Windows).
3. You will need to create a file called 'private.php' directly in the 'CP476' directory. This file should contain the following code:
```php
<?php
# MySQL Database Connection
CONST HOST = 'your_host';               # ex.'localhost'
CONST USERNAME = 'your_sql_username';   # ex.'root'
CONST PASSWORD = 'your_sql_password';   
CONST DB_NAME = 'your_db_name';         # ex.'my_grading_app'
?>
```
3. Ensure the 'mysqli' and 'pdo_mysql' PHP extensions are enabled in your 'php.ini' file ('C:\php\php.ini' on Windows).
4. Start the Apache server and navigate to '[localhost/CP476/index.php](http://localhost/CP476/index.php)' in your web browser.
5. You're all set! :)

## Database Setup

The database is initialized automatically when you first run the app.

All default database values are stored in 'CP476/src/db/defaults/'. 

Default user credentials can be changed in 'CP476/src/db/defaults/auth.txt'.

  * Default admin credentials:

    * Username: admin

    * User ID:  123456789

  * Default instructor credentials:

    * Username: user

    * User ID:  111111111

  * Default student credentials:
  
      * Any Student Name and Student ID in 'CP476/src/db/defaults/name.txt' can be used to login as a student.

## Usage

For all accounts, their is a 'Logout' button at the top of the page that will end your session.

The application caches table data to the web browser. If data appears stale, pressing the 'Clear Filters' button for any table will refresh the cache using a new call to the database.

For testing purposes, there is currently a 'Clear Everything' button on the main page that will clear the session, delete all records from the database, and reinitialize the app with default values.

### Instructors
* If you already have an account: login with your Username  and User ID.
* If you are not registered, an admin must register your details before you can login.
* Navigate to various pages by selecting either 'Students', 'Courses', or 'Grades' from the navigation bar.
* Each page displays a table of information pulled from the database, and allows you to add, edit, delete, or search through records.

### Administrators
* Login with your Username and User ID.
* Access all of the same features as an instructor, plus the additional 'Users' tab where you can view, add, edit, delete, or search authorized users, such as instructors and other administrators.

### Students
* Login with your Full Name and Student ID.
* Access a table of your grades for all courses you are registered in.

## Video Demonstration

> [CP476 Final Project Demo (YouTube Video)](https://www.youtube.com/watch?v=6ppifiezMik)

## License

This project is licensed under the GNU General Public License - see the [LICENSE](LICENSE) file for details.