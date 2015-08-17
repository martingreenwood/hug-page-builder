<?php

// require settings
require_once('incs/settings.php'); 

// begin our session 
session_start();

// connect to database
$mysql_hostname = DB_HOST;
$mysql_username = DB_USER;
$mysql_password = DB_PASS;
$mysql_dbname = DB_NAME;

if (isset($_GET['id'])) {

    try
    {

        $dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_dbname", $mysql_username, $mysql_password);
        // $message = a message saying we have connected

        // set the error mode to excptions
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // prepare the insert
        $stmt = $dbh->prepare("DELETE FROM pages WHERE id = :id");

        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);   

        $stmt->execute();

        // that's t we're done here - use $row to make your loop etc.

        header('Location: app.php?deleted');
    }
    catch(Exception $e)
    {
        header('Location: app.php?error');; 
    }

} else {
    header('Location: app.php?oops');
}
?>