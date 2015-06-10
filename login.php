<?php

// require settings
require_once('incs/settings.php'); 

// begin our session
session_start();

// check if the users is already logged in
if(isset( $_SESSION['user_id'] ))
{
    header("Location: app.php");
}

// check that both the username, password have been submitted 
if(!isset( $_POST['username'], $_POST['password']))
{
    $message = 'Please enter a valid username and password';
}

else
{
    // if we are here the data is valid and we can check the database 
    $phpro_username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $phpro_password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

    // now we can encrypt the password
    $phpro_password = sha1( $phpro_password );
    
    // connect to database ***/
    $mysql_hostname = DB_HOST;
    $mysql_username = DB_USER;
    $mysql_password = DB_PASS;
    $mysql_dbname = DB_NAME;

    try
    {
        $dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_dbname", $mysql_username, $mysql_password);
        // $message = a message saying we have connected

        // set the error mode to excptions
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // prepare the select statement
        $stmt = $dbh->prepare("SELECT id, username, password FROM sheep 
                               WHERE username = :username AND password = :password");

        // bind the parameters
        $stmt->bindParam(':username', $phpro_username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $phpro_password, PDO::PARAM_STR, 40);

        // execute the prepared statement
        $stmt->execute();

        // check for a result
        $user_id = $stmt->fetchColumn();

        // if we have no result then fail boat
        if($user_id == false)
        {
                $message = 'Login Failed';
        }
        // if we do have a result, all is well
        else
        {
                // set the session user_id variable
                $_SESSION['user_id'] = $user_id;

                // tell the user we are logged in or redirect to app page
                $message = 'You are now logged in';
                header("Location: app.php");
        }

    }
    catch(Exception $e)
    {
        // if we are here, something has gone wrong with the database
        $message = 'We are unable to process your request. Please try again later"';
    }
}
?>

<!DOCTYPE html>

<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home / Login</title>

    <?php include("incs/head.php"); ?>
        
    </head>
    
    <body>
        
        <div class="container">

            <div class="alert alert-info" role="alert"><?php echo $message; ?></div>

        </div> <!-- /container -->

        <?php include("incs/footer.php"); ?>
    </body>
</html>
