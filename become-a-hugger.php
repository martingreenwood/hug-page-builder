<?php

// require settings
require_once('incs/settings.php'); 

// begin our session 
session_start();

// set a form token
$form_token = md5( uniqid('auth', true) );

// set the session from the token
$_SESSION['form_token'] = $form_token;

?>
<!DOCTYPE html>

<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add a user!</title>

    <?php include("incs/head.php"); ?>
        
    </head>
    
    <body>
        
        <div class="container text-center">

        <div class="page-header">
            <h1>Add a user</h1>
        </div>

            <form class="form-signin" action="add_user.php" method="post">
                
                <input type="hidden" name="form_token" value="<?php echo $form_token; ?>" />

                <input type="email" id="username" name="username" class="form-control" placeholder="username (email)" maxlength="255" required autofocus>
                <input type="password" id="username" name="password" class="form-control" placeholder="Password" maxlength="20" required>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Add User</button>
            </form>

        </div> <!-- /container -->

        <?php include("incs/footer.php"); ?>
    </body>
</html>
