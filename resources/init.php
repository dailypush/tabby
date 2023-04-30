<?php
function initializeDatabaseConnection($dsn, $db_username, $db_password)
{
	try {
		if (strpos($dsn, 'mysql') !== false) {
			$db = new PDO($dsn, $db_username, $db_password, [
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
				PDO::MYSQL_ATTR_FOUND_ROWS => true
			]);
		} else {
			$db = new PDO($dsn, $db_username, $db_password);
		}
		return $db;
	} catch (PDOException $e) {
		displayErrorAndExit('Something went wrong while connecting to the database.');
	}
}

function displayErrorAndExit($errorMessage)
{
	include('templates/header.php');
	$error = $errorMessage;
	include('templates/error.php');
	include('templates/footer.php');
	exit;
}

ini_set('display_errors', '0');
$db = initializeDatabaseConnection($dsn, $db_username, $db_password);
session_start();