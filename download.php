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

// set $id from query
if (isset($_GET['id'])): 
    $id = $_GET['id']; 
endif;



try
{
    $dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_dbname", $mysql_username, $mysql_password);
    // $message = a message saying we have connected

    // set the error mode to excptions
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // prepare the select
    $stmt = $dbh->prepare("SELECT * FROM pages WHERE id=$id");

    // execute the prepared statement
    $stmt->execute();

    //fetch the data and store it in a var
    $pages = $stmt->fetch(PDO::FETCH_OBJ);

    // that's t we're done here - use $row to make your loop etc.

}
catch(Exception $e)
{
    // display errors
    echo $e; 
}

?>

<?php

// get variables from returned object
$pagename = $pages->downloadable;

$dir =  __DIR__ . "/pages/";

$dFile = $dir.$pagename;

if (file_exists($dFile)) {

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename=\"" . $pagename . "\";");
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    //header('Content-Length: ' . filesize($pagename));
    ob_clean();
    flush();
    readfile($dFile); //showing the path to the server where the file is to be download
    exit;

}


?>