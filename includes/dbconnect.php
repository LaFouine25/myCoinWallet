<?php
// Connection BDD
$DBServer = 'localhost'; 
$DBUser   = 'xxx';
$DBPass   = 'xxx';
$DBName   = 'xxx';

// Connection DB
$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);

// check connection
if ($conn->connect_error)
{
	trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
	$DBIsCo = false;
}
else
{
	$DBIsCo = true;
}
?>