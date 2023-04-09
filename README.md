# CP476 Final Project
### A simple grading app for teachers.

This web app is built using PHP and runs on an Apache web server.

## Installation

1. Install [PHP](https://windows.php.net/qa/) and [Apache](https://www.apachelounge.com/download/).
2. Ensure that the `C:\Apache24\conf\httpd.conf` file has been configured for using PHP (add the following lines):
```apacheconf
# Execute PHP files
PHPIniDir "C:\php"
AddHandler application/x-httpd-php .php
<FilesMatch \.php$>
      SetHandler application/x-httpd-php
</FilesMatch>
LoadModule php_module "C:\php\php8apache2_4.dll"
```
3. In `php.ini` (`C:\php\php.ini` on Windows), ensure that the PHP extension folder is set correctly (e.g. `extension_dir = "C:/php/ext/"`) and that the 'mysqli' and 'pdo_mysql' PHP extensions are enabled by deleting the "`;`" on front of each line, if there is one.
4. Clone this repository directly into your Apache web server's root directory (`C:\Apache24\htdocs\` on Windows) and name the folder `CP476`.
5. You will need to create a file called `private.php` directly in the `[...]\CP476\` directory. This file should contain the following code:
```php
<?php
# MySQL Database Connection
CONST HOST = 'your_host';               # ex.'localhost'
CONST USERNAME = 'your_mysql_username'; # ex.'root'
CONST PASSWORD = 'your_mysql_password'; # or null if your mysql does not use a password
CONST DB_NAME = 'your_db_name';         # ex.'my_grading_app'
?>
```
6. Start the Apache server and navigate to [localhost/CP476/index.php](http://localhost/CP476/index.php) in your web browser.
7. You're all set! :)

## Database Setup

The database is initialized automatically when you first run the app.

All default database values are stored in `[...]\CP476\src\db\defaults\`. 

## Usage

* Navigate to various pages by selecting either `Names`, `Courses`, or `Grades` from the navigation bar.
* Each page displays a table of information pulled from the database.
* The `Names` and `Courses` pages allow you to edit details for a row and press `Save Changes` to update the record in the database.

## License

This project is licensed under the GNU General Public License - see the [LICENSE](LICENSE) file for details.
