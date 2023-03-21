<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>My Classroom Grading Application</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php include_once(__DIR__.'/style.php'); ?>
</head>
<body>
<?php
// Setup Session Storage
session_start();

// Setup Database
include_once(__DIR__.'/../db/init_db.php');
init_db();
?>