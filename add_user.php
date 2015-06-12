<?php

// require settings
require_once('incs/settings.php'); 

// begin our session
session_start();

// first check that both the username, password and form token have been sent
if(!isset( $_POST['username'], $_POST['password'], $_POST['form_token'])) 
{
    $message = 'Please enter a valid username and password';
}
// check the form token is valid
elseif( $_POST['form_token'] != $_SESSION['form_token'])
{
    $message = 'Invalid form submission';
}
// check the username is the correct length
elseif (strlen( $_POST['username']) > 255 || strlen($_POST['username']) < 4)
{
    $message = 'Incorrect Length for Username';
}
// check the password is the correct length
elseif (strlen( $_POST['password']) > 255 || strlen($_POST['password']) < 4)
{
    $message = 'Incorrect Length for Password';
}
// check the password has printable character(s) except space
elseif (ctype_graph($_POST['password']) != true)
{
        // if there is no match
        $message = "Password must be alpha numeric";
}
else
{
    // if we are here the data is valid and we can insert it into database
    $phpro_username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $phpro_password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

    // now we can encrypt the password
    $phpro_password = sha1( $phpro_password );
    
    // connect to database
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

        // prepare the insert
        $stmt = $dbh->prepare("INSERT INTO sheep (username, password ) VALUES (:username, :password )");

        // bind the parameters
        $stmt->bindParam(':username', $phpro_username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $phpro_password, PDO::PARAM_STR, 40);

        // execute the prepared statement
        $stmt->execute();

        // unset the form token session variable
        unset( $_SESSION['form_token'] );

        // if all is done, say thanks
        $message = 'New user added';
    }
    catch(Exception $e)
    {
        // check if the username already exists
        if( $e->getCode() == 23000)
        {
            $message = 'Username already exists';
        }
        else
        {
            // if we are here, something has gone wrong with the database
            $message = 'We are unable to process your request. Please try again later"';
        }
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